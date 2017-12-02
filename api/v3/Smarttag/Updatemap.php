<?php
//use CRM_Smartgrouptag_ExtensionUtil as E;

/**
 * Smarttag.Updatemap API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_smarttag_Updatemap_spec(&$spec) {
}

function split_strings($big_string) {

  $strings = explode("\n", $big_string);
  $result = array();

  foreach ($strings as $string) {
    $pair = explode(',', $string);
    $result[ltrim(rtrim($pair[0]))] = ltrim(rtrim($pair[1]));
  };

  return $result;

}

function empty_map_table() {

  $params = array(
    'sequential' => 1,
    'version' => 3,
    'rowCount' => 0,
  );
  $rows = civicrm_api3("Smarttag","get", $params)['values'];
  foreach ($rows as $row) {
    $params = array(
      'id' => $row['id'],
    );
    civicrm_api3('Smarttag', 'delete', $params);

  }
  
  
}

function create_map($tag_map) {
  foreach ($tag_map as $tag_name => $group_name) {

    $tag_id = get_tag_id ($tag_name);
    $group_id = get_group_id ($group_name);
    if ($tag_id && $group_id) {

      $params = array(
        'tag_id' => $tag_id,
        'group_id' => $group_id,
      );

      civicrm_api3('Smarttag', 'create', $params);
      log_success("Success. Tag name: ".$tag_name." Group name: ".$group_name);
    }
    else {
      log_error("Invalid mapping: ".$tag_name." : ".$group_name);
      CRM_Core_Session::setStatus("Invalid mapping: ".$tag_name." : ".$group_name, ts('Update'), 'no-popup');
    }
  }
}

/**
 * Smarttag.Updatemap API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_smarttag_Updatemap($params) {
  include 'Updatetags.php';
  $tag_map = split_strings($params['tag_map']);
//  CRM_Core_Session::setStatus(ts(json_encode($tag_map)), ts('Update'), 'no-popup');
  empty_map_table();
  create_map($tag_map);
//  CRM_Core_Session::setStatus("Something done!", ts('Update'), 'no-popup');
  return civicrm_api3_create_success(array(), $params, 'Smarttag', 'Updatemap');
}
