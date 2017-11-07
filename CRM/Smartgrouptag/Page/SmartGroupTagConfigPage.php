<?php
use CRM_Smartgrouptag_ExtensionUtil as E;

class CRM_Smartgrouptag_Page_SmartGroupTagConfigPage extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('SmartGroupTagConfigPage'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));

    parent::run();
  }

}
