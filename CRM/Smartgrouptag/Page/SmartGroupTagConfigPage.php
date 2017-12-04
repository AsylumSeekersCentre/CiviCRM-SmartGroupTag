<?php
use CRM_Smartgrouptag_ExtensionUtil as E;

function display_message($message) {
  CRM_Core_Session::setStatus(ts($message), 'Success', 'no-popup');
}

function get_single_name($v) {
  $result = "";
  foreach ($v as $key => $value) {
    if ($value['name']) {
      $result = $value['name'];
    }
  };
  return $result;
}

function get_single_title($v) {
  $result = "";
  foreach ($v as $key => $value) {
    if ($value['name']) {
      $result = $value['title'];
    }
  };
  return $result;
}

function get_tag_name($tag_id) {
  $params = array(
    'id' => $tag_id,
    'sequentual' => 1,
  );

  $raw = civicrm_api3('Tag', 'get', $params);
  $name = get_single_name($raw['values']);
  return $name;
}

function get_group_name($id) {
  $params = array(
    'id' => $id,
  );

  $raw = civicrm_api3('Group', 'get', $params);
  $name = get_single_title($raw['values']);
  return $name;
}

function get_names($raw) {
  $result = array(
    'tag_id' => get_tag_name($raw['tag_id']),
    'group_id' => get_group_name($raw['group_id']),
  );
  return $result;
}

class CRM_Smartgrouptag_Page_SmartGroupTagConfigPage extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('SmartGroupTag Config Page'));

    // Example: Assign a variable for use in a template
    $params = array(
      'sequential' => 1,
      'rowCount' => 0
    );
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    $raw = civicrm_api3("Smarttag", "get", $params)['values'];
    $tagMap = array_map(get_names, $raw);
    $this->assign('tagMap', $tagMap);
//    $this->assign('tagMap', civicrm_api3("Smarttag", "get", $params)['values']);
    $this->assign('tags', civicrm_api3("Tag", "get", $params)['values']);
    $this->assign('groups', civicrm_api3("Group", "get", $params)['values']);
//    CRM_Core_Session::setStatus(json_encode (civicrm_api3("Smarttag", "get", $params)['values']), 'Error', 'no-popup');

    parent::run();
  }

}
