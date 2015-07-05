<?php

class Fredi extends WireData implements Module {

  public static function getModuleInfo() {
    return array(
      'title' => 'Fredi - friendly frontend editing for PW',
      'version' => 120,
      'summary' => 'Adds frontend editing possibilities for ProcessWire',
      'singular' => false,
      'autoload' => false,
      'installs' => array("FrediProcess")
      );
  }

  public $enabled = TRUE;
  public $linkText;
  public $linkClass;
  public $hideTabs;

  protected $pageContext;
  protected $frediFields;


  public function init() {
    $this->reset();
  }

  public function reset() {
    $this->linkText = $this->_("edit");
    $this->linkClass = "";
    $this->hideTabs = "";
  }

  /*
   *
   * @return Required <link> and <script> tags that are intended to go inside sites <head> tag.
   */
  public function renderScript($hoverLinks = true) {
    $out  = "<link rel='stylesheet' href='". wire('config')->urls->siteModules ."Fredi/modal/modal.css' />\n\t";
    $out .= "<script src='". wire('config')->urls->siteModules ."Fredi/modal/modal.js'></script>";
    if ($hoverLinks) {
      $out .= "<link rel='stylesheet' href='". wire('config')->urls->siteModules ."Fredi/css/fredi.css' />\n\t";
      $out .= "<script src='". wire('config')->urls->siteModules ."Fredi/js/fredi.js'></script>";
    }
    return $out;
  }

  public function setText($linkText) {
    $this->linkText = $linkText;
    return $this;
  }

  public function setClass($linkClass) {
    $this->linkClass = $linkClass;
    return $this;
  }

  public function hideTabs($tabs) {
    $this->hideTabs = $tabs;
    return $this;
  }

  /*
   * Renders edit link that will open editing modal for wanted fields. Takes additional parameter for page context.
   *
   * @param string $fields one or more fieldnames. Multiple fields separeted with pipe: | Ie. "headline|title"
   * @param Page $pageContext Page that should be edited. Give page context if you are editing other page than the one currently viewed
   * @param string $linkText Custom text for edit link
   *
   * @return mixed link to edit view/modal or nothing if editing is not allowed
   */
  public function render($fields, Page $pageContext = null) {
    if (is_null($pageContext)) $this->pageContext = wire('page');
    else $this->pageContext = $pageContext;

    $this->frediFields = $fields;

    if ( ! $this->pageContext->editable()) return;

    return $this->_renderLink();
  }

  public function renderAll(Page $pageContext = null) {
    if (is_null($pageContext)) $this->pageContext = wire('page');
    else $this->pageContext = $pageContext;

    if ( ! $this->pageContext->editable()) return;

    $this->frediFields = "frediAll";

    return $this->_renderLink();
  }

  public function addPage($template, $fields = "title", Page $parent = null) {
    if ($parent instanceof NullPage || is_null($parent)) $parent = wire('page');
  
    if ( ! $parent->addable()) return;
    
    $fieldEdit = wire('pages')->get("template=admin, name=fredi-field-edit");

    $userLanguageParam = $this->_getLanguageParam();
    $url = "{$fieldEdit->httpUrl}add/?parent_id={$parent->id}&template={$template}&fields={$fields}&modal=1$userLanguageParam";
    $onClick = "onclick='fredi.modal(\"{$url}\"); return false;'";

    return "<a class='fredi-add {$this->linkClass}' href='{$url}' $onClick>{$this->linkText}</a>";
  }

  public function editable() {

    $fieldsArray = explode("|", $this->frediFields);

    foreach($fieldsArray as $fieldName) {
      // Check if page has the field that wants to be edited
      if ( ! $this->pageContext->fields->has($fieldName)) return FALSE;

      // Check if the current user has rights to edit that field
      if ( ! $this->pageContext->editable($fieldName)) return FALSE;

    }
    return TRUE;
  }

  public function __get($fieldName) {

    if ( ! $this->enabled) return;

    $this->pageContext = wire('page');
    $this->frediFields = $fieldName;

    if ( ! $this->editable()) return;
    return $this->_renderLink();
  }

  public function __call($fieldName, $args) {

    if ( ! $this->enabled) return;

    $this->frediFields = $fieldName;

    // Set the pageContext
    if ( ! isset($args[0])) $this->pageContext = wire('page');
    else if ( $args[0] instanceof Page) $this->pageContext = $args[0];
    if ( ! $this->pageContext->id ) throw new WireException("Couldn't set the page context");

    // See if the fields are editable
    if ( ! $this->editable()) return;

    return $this->_renderLink();
  }

  private function _getLanguageParam() {
    if(wire("modules")->isInstalled("LanguageSupportFields") && !wire("user")->language->isDefault()) {
      return "&language=" . wire("user")->language->id;
    }
  }

  private function _renderLink() {
    $fieldEdit = wire('pages')->get("template=admin, name=fredi-field-edit");

    // if language support fields is installed
    // we attach additional GET param for the languagefieldtabs module
    // or possibly other modules can pick this up. If default language do nothing
    $userLanguageParam = $this->_getLanguageParam();
    
    $url = "{$fieldEdit->httpUrl}?id={$this->pageContext->id}&frediFields={$this->frediFields}&hideTabs={$this->hideTabs}&modal=1$userLanguageParam";

    // Nice classes and id to links for easy styling and keep scroll position after refresh
    $fieldsClass = "fredi-" . $this->pageContext->id . "-" . str_replace("|", "-", $this->frediFields);

    // Attach modal to onclick event
    $onClick = "onclick='fredi.modal(\"{$url}\"); return false;'";

    $returnString = "<span class='frediwrapper'><a class='fredi {$this->linkClass} {$fieldsClass}' href='{$url}' $onClick>". $this->linkText ."</a></span>";

    return $returnString;
  }
}