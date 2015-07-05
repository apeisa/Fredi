<?php

/**
 *
 * Copyright 2013-2015 by Antti Peisa
 *
 *
 * ProcessWire 2.3, 2.4, 2.5, 2.6 
 * Copyright (C) 2012 by Ryan Cramer 
 * Licensed under GNU/GPL v2, see LICENSE.TXT
 * 
 * http://processwire.com
 *
 */

class FrediProcess extends Process implements WirePageEditor {

  public static function getModuleInfo() {
    return array(
      'title' => 'Fredi - edit page process', 
      'summary' => 'Process module that Fredi uses for page editing.', 
      'version' => 120, 
      'author' => 'Antti Peisa', 
      'permission' => 'page-edit', 
      'requires' => array('Fredi'),
      ); 
  }

  const pageName = 'fredi-field-edit';
  
  public $pageId;
  public $pageContext;
  public $frediFields;
  public $fieldsArray = array();

  public function init() {
    parent::init(); // required
  }

  public function ___execute() {
  
    $this->pageId = (int) $this->input->get("id");
    $this->frediFields = $this->input->get("frediFields");
    $this->fieldsArray = explode("|", $this->frediFields);
    $this->hideTabsArray = explode("|", $this->input->get->hideTabs);

    // We basically replicate the ProcessPageEdit here
    $pe = $this->modules->get('ProcessPageEdit');

    // Pagetable or similar
    if ( ! $this->frediFields) {
      return $pe->execute();
    }
    
    $this->pageContext = $this->pages->get($this->pageId);
    
    // Check if there is not such a page found
    if ( ! $this->pageContext->id) throw new WireException("Page not found");
    
    // Check that this page is editable by current user
    if ( ! $this->pageContext->editable()) throw new WireException("You don't have right to edit this page");
    

    // ...but we keep frediFields in get params
    $pe->addHookAfter('buildForm', $this, 'alterAction'); 

    // ...and modify the page edit form to contain only wanted fields
    $pe->addHookAfter('buildFormContent', $this, 'filterFields'); 

    // ...and finally we do javascript "refresh" instead traditional redirect
    $pe->addHookBefore('processSaveRedirect', $this, 'saveRedirect'); 

    if ($this->hideSettingsTab || in_array("settings", $this->hideTabsArray)) $pe->addHookBefore('buildFormSettings', $this, 'hideTab');
    if ($this->hideChildrenTab || in_array("children", $this->hideTabsArray)) $pe->addHookBefore('buildFormChildren', $this, 'hideTab');
    if ($this->hideDeleteTab || in_array("delete", $this->hideTabsArray)) $pe->addHookBefore('buildFormDelete', $this, 'hideTab');
    
    return $pe->execute();
  }

  public function executeAdd() {


    $parent_id = (int)$this->input->get->parent_id;
    $parent = $this->pages->get($parent_id);
    $template = $this->input->get->template;
    $fields = $this->input->get->fields;
    $fieldsArray = explode("|", $fields);

    $out = "<h2>" . sprintf(__("Adding new page under %s"), $parent->title) . "</h2><p>{$parent->path}</p>";

    $p = new Page();
    $p->template = $template;
    $p->parent = $parent;
    

    $form = $this->modules->get("InputfieldForm");
    $form->method = "post";
    $form->action = "./?parent_id={$parent_id}&template={$template}&fields={$fields}&modal=1";
    foreach ($fieldsArray as $field) {
      $f = $this->fields->get($field)->getInputfield($p);
      $form->add($f);
    }
    $submit = $this->modules->get("InputfieldSubmit");
    $submit->value = __("Create new page");
    $submit->attr("name", "submit");
    $form->add($submit);

    if ($this->input->post->submit) {
      $form->processInput($this->input->post);
      if($form->getErrors()) {
        $out .= $form->render();
      } else {
        foreach ($fieldsArray as $field) {
          $p->$field = $form->get($field)->value;
        }
        $p->save();
        return "<script>window.parent.fredi.empty(); window.parent.location.replace('{$p->httpUrl}');</script>";
      }
    } else {
      $out .= $form->render();
    }

    return $out;
  }

  // WirePageEditor requires this one
  public function getPage() {
    return $this->pages->get((int) $this->input->get->id);
  }

  public function hideTab(HookEvent $event) {
    $event->replace = true;
    $wrapper = new InputfieldWrapper;
    $event->return = $wrapper;
  }

  public function alterAction(HookEvent $event) {
    $form = $event->return;
    $form->attr("action", "./?id={$this->pageId}&modal=1&frediFields={$this->frediFields}"); 
    $event->return = $form;   
  }

  // Loop through fields and remove the ones that we don't want to edit. Also some sanity checks here.
  public function filterFields(HookEvent $event) {

    // If we want all fields, we don't alter the form
    if ($this->frediFields == "frediAll") return;

    $inputfields = $event->return;
    
    
    foreach($this->fieldsArray as $fieldName) {
      // Check if page has the field that wants to be edited
      if ( ! $this->pageContext->fields->has($fieldName)) throw new WireException("There is no field $fieldName on editable page");
        
      // Check if the current user has rights to edit that field
      if ( ! $this->pageContext->editable($fieldName)) throw new WireException("You don't have rights to edit field $fieldName");
    }

    foreach($inputfields as $field) {
      if ( ! in_array($field->name, $this->fieldsArray)) {
        $inputfields->remove($field);
      }
    }
    
    $event->return = $inputfields;
  }

  // After page save, empty the modal (to make visual difference right after save) and do js redirect on parent window
  public function saveRedirect(HookEvent $event) {
    $event->replace = true;
    echo "<script>window.parent.fredi.empty(); window.parent.location.reload();</script>";
  }

  public function ___install() {

    // create the page our module will be assigned to
    $page = new Page();
    $page->template = 'admin';
    $page->name = self::pageName; 

    // installs under admin => pages
    $page->parent = $this->pages->get($this->config->adminRootPageID)->child('name=page');
    $page->process = $this; 

    // we will make the page title the same as our module title
    $info = self::getModuleInfo();
    $page->title = $info['title'];
    $page->addStatus(Page::statusHidden) ;

    // support for multi-languages, set the status[langid] of alternative languages
    // to active (1) so we can get the url when editing alternative languages / @soma
    if($this->modules->isInstalled("LanguageSupportPageNames")){
      if(count($this->languages) > 1){
        foreach($this->languages as $lang){
          if(!$lang->isDefault()) $page->set("status$lang->id",1);
        }
      }
    }

    // save the page
    $page->save();

    // tell the user we created this page
    $this->message("Created Page: {$page->path}"); 
  }

  public function ___uninstall() {
    $moduleID = $this->modules->getModuleID($this); 
    $page = $this->pages->get("template=admin, process=$moduleID, name=" . self::pageName); 

    if($page->id) {
      $this->message("Deleting Page: {$page->path}"); 
      $page->delete();
    }
  }
  
}