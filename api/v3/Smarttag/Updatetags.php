<?php

/**
 * Smarttag.Updatetags API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_smarttag_Updatetags_spec(&$spec) {
// There are no required arguments for this.
//  $spec['magicword']['api.required'] = 1;
}

function log_error($message) {
  CRM_Core_Error::debug_log_message($message);
//  CRM_Core_Session::setStatus(ts($message), 'Error', 'no-popup');
}

function display_message($message) {
  CRM_Core_Session::setStatus(ts($message), 'Success', 'no-popup');
}

function log_success($message) {
  CRM_Core_Error::debug_log_message($message);
//  CRM_Core_Session::setStatus(ts($message), 'Success', 'no-popup');
}

function display_json($v) {
  display_message (json_encode($v));
}

function echo_json($v) {
  echo '<pre>' . json_encode($v) . '</pre>';
}

function get_group_id($group_name) {

  $params = array(
    'sequential' => 1,
    'title' => $group_name,
    'rowCount' => 0,
  );

  $table = civicrm_api3('Group', 'get', $params);

  $result = null;
  foreach ($table['values'] as $mgroup) {
    if ($mgroup['title'] == $group_name) {
      $result = $mgroup['id'];
    }
  };

  return $result;

}

function contact_get_smart_group_id($group_id) {

  if ($group_id == null) {
    // Error log will be handled by the exception resulting from processing
    // the null return value later. This will only skip the bad mapping, it
    // will not abort the whole process.
    return null;
  }

  else {
   $params = array(
    'rowCount' => 0,
    'group' => array(
      'IN' => array(
        '0' => $group_id,
      ),
    )
   );

   $result = civicrm_api3('Contact', 'get', $params);
   return $result['values'];

  }

}

function contact_get_smart_group($group_name) {

  $group_id = get_group_id($group_name);
  return contact_get_smart_group_id($group_id);

}

function get_tag_id ($tagname) {

  $tag_id = null;
  $tag_record = civicrm_api3('Tag', 'get', array(
    'name' => $tagname,
    'rowCount' => 0,
  ));

  // This foreach is expected to unpack a single result.
  foreach($tag_record['values'] as $value) { 
    if ($value['name'] == $tagname) {
      $tag_id = $value['id'];
    }
  };

  return $tag_id;
}

function add_tag_to_contact ($tag_id, $contact_id) {

  $params = array(
    'contact_id' => $contact_id,
    'tag_id' => $tag_id,
  );

  $result = civicrm_api3('EntityTag', 'create', $params);
  return $result;
}

function add_tag_to_contacts($tag_id, $contacts) {

  $tally = 0;

  foreach ($contacts as $contact_id => $contact) {
    add_tag_to_contact ($tag_id, $contact_id);
    $tally += 1;
  };

  return $tally;

}

function split_file_strings($strings) {

  $result = array();

  foreach ($strings as $string) {
    $pair = explode(',', $string);
    $result[ltrim(rtrim($pair[0]))] = ltrim(rtrim($pair[1]));
  };

  return $result;

}

function load_map($filename) {
  $res = CRM_Core_Resources::singleton();
  $path = $res->getUrl('au.org.asylumseekerscentre.smartgrouptag') . $filename;
  $strings = file($path);
  $map = split_file_strings($strings);
  return $map;
}

function get_tagged_contacts ($tag_id) {

  $contactParams = array(
    'version' => 3,
    'tag'=> $tag_id,
    'rowCount' => 0,
  );

  return civicrm_api3("Contact","get", $contactParams)['values'];

}

function delete_tag_from_contact($tag_id, $contact_id) {

  $params = array(
    'contact_id_h' => $contact_id,
    'tag_id' => $tag_id,
  );

  $result = civicrm_api3('EntityTag', 'delete', $params);
  return $result;

}

function delete_tag_from_contacts($tag_id, $contacts) {

  $tally = 0;

  foreach ($contacts as $contact_id => $contact_data) {
    delete_tag_from_contact ($tag_id, $contact_id);
    $tally += 1;
  };

  return $tally;
  
}

function subtract_contacts($arr1, $arr2) {
  $result = array();

  foreach ($arr1 as $contact_id => $contact_data) {
    if (!array_key_exists($contact_id, $arr2)) {
      $result[$contact_id] = $contact_data;
    }
  };

  return $result;

}

function delete_and_apply_tags($tag_map) {

  $tally = array();

  foreach ($tag_map as $tagname => $smart_group) {

    try {

      $tag_id = get_tag_id ($tagname);
      $tagged_contacts = get_tagged_contacts($tag_id);
      $sgroup_contacts = contact_get_smart_group ($smart_group);

      $contacts_to_delete_tag = subtract_contacts ($tagged_contacts, $sgroup_contacts);
      $contacts_to_add_tag = subtract_contacts ($sgroup_contacts, $tagged_contacts);
      $delete_tally = delete_tag_from_contacts ($tag_id, $contacts_to_delete_tag);
      $add_tally = add_tag_to_contacts ($tag_id, $contacts_to_add_tag);

      $tally[$tagname]['delete'] = $delete_tally;
      $tally[$tagname]['add'] = $add_tally;
      $tally[$tagname]['confirm'] = sizeof($sgroup_contacts) - $add_tally;
    }

    catch (CiviCRM_API3_Exception $e) {
      $error_message = 'Could not process tag ' . $tagname . ':  ' . $e->getMessage();
      log_error($error_message);
    }

  }

  return $tally;
  
}

function delete_and_apply_tags_from_table() {

  $tally = array();

  $tag_map = civicrm_api3("Smarttag", "get", array() )['values'];

  foreach ($tag_map as $map_id) {

    try {
      $tag_id = $map_id['tag_id'];
      $group_id = $map_id['group_id'];

      $tagged_contacts = get_tagged_contacts($tag_id);
      $sgroup_contacts = contact_get_smart_group_id ($smart_group_id);

      $contacts_to_delete_tag = subtract_contacts ($tagged_contacts, $sgroup_contacts);
      $contacts_to_add_tag = subtract_contacts ($sgroup_contacts, $tagged_contacts);
      $delete_tally = delete_tag_from_contacts ($tag_id, $contacts_to_delete_tag);
      $add_tally = add_tag_to_contacts ($tag_id, $contacts_to_add_tag);

      $tally[$tag_id]['delete'] = $delete_tally;
      $tally[$tag_id]['add'] = $add_tally;
      $tally[$tag_id]['confirm'] = sizeof($sgroup_contacts) - $add_tally;
    }

    catch (CiviCRM_API3_Exception $e) {
      $error_message = 'Could not process tag ' . $tag_id . ':  ' . $e->getMessage();
      log_error($error_message);
//      CRM_Core_Session::setStatus(ts($error_message), ts('apply_tags failure in SmartTag.UpdateTags'), 'no-popup');
    }
  }

  return $tally;
  
}

/**
 * Smarttag.Updatetags API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_smarttag_Updatetags($params) {

  try {
    $starttime = time();
//    $tag_map = load_map("map.txt"); // to use file instead
    $tally = delete_and_apply_tags_from_table();
    $endtime = time();
    $time_taken = $endtime - $starttime;
    $tally['time taken'] = $time_taken;
    $message = 'UpdateTags Success in ' . $time_taken . ' seconds: ' . json_encode($tally);
    log_success ($message);
//    header("Refresh:0"); // FIXME I want to refresh the page to display status messages, but this does not work.
    return civicrm_api3_create_success($tally, $params, 'Smarttag', 'Updatetags');
  }

  catch (CiviCRM_API3_Exception $e) {
    // Handle errors here.
    $error_message = $e->getMessage();
    return civicrm_api3_create_error($error_message);
  }

}

