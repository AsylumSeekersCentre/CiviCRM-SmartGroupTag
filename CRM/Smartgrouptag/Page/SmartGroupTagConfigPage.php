<?php
use CRM_Smartgrouptag_ExtensionUtil as E;

class CRM_Smartgrouptag_Page_SmartGroupTagConfigPage extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('SmartGroupTagConfigPage'));

    // Example: Assign a variable for use in a template
    $params = array(
      'sequential' => 1,
    );
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    $this->assign('tagMap', civicrm_api3("Smarttag", "get", $params).values);
    $this->assign('tags', civicrm_api3("Tag", "get", $params)['values']);
    $this->assign('groups', civicrm_api3("Group", "get", $params)['values']);

    parent::run();
  }

}
