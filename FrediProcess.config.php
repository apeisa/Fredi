<?php

class FrediProcessConfig extends ModuleConfig {

  public function __construct() {

    $this->add(array(
      array(
        'name' => 'hideSettingsTab', // name of field
        'type' => 'checkbox', // type of field (any Inputfield module name)
        'label' => $this->_('Always hide settings tab'), // field label
      ),
      array(
        'name' => 'hideChildrenTab', // name of field
        'type' => 'checkbox', // type of field (any Inputfield module name)
        'label' => $this->_('Always hide children tab'), // field label
      ),
      array(
        'name' => 'hideDeleteTab', // name of field
        'type' => 'checkbox', // type of field (any Inputfield module name)
        'label' => $this->_('Always hide delete tab'), // field label
      ),
    )); 
  }
}
