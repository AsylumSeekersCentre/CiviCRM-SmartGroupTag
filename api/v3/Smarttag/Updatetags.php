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
//  $spec['magicword']['api.required'] = 1;
}

function log_message($message) {
  CRM_Core_Error::debug_log_message($message);
}

function log_json($v) {
  log_message (json_encode($v));
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

function contact_get_smart_group($group_name) {
  $group_id = get_group_id($group_name);
//  CRM_Core_Session::setStatus('Got group id ' . $group_id . ' for group ' . $group_name, 'Success', 'no-popup');
  if ($group_id == null) {
    $error_message = 'No group named ' . $group_name;
    log_message($error_message);
    CRM_Core_Session::setStatus(ts($error_message), ts('contact_get_smart_group failure in SmartTag.UpdateTags'), 'no-popup');
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
// This also works and is simpler than the above. Why is the above structure
// recommended in the examples? It would make sense if we wanted to check if
// the contact is in any of a set of groups, but for a single group I would
// prefer the simple structure below, unless I'm missing something.
//    $params = array();
//    $params['group'] = $group_id;
   
//    CRM_Core_Session::setStatus('About to get contacts', 'Success', 'no-popup');
    $result = civicrm_api3('Contact', 'get', $params);
//    CRM_Core_Session::setStatus('Returning contacts', 'Success', 'no-popup');
    return $result['values'];
  }
}

function get_tag_id ($tag) {
  $data = civicrm_api3('Tag', 'get', array(
    'sequential' => 1,
    'name' => $tag,
    'rowCount' => 0,
  ));
  return $data['id'];
}

function add_tag_to_contact ($tag, $contact_id) {
//  echo 'Adding tag ' . $tag . ' to contact ' . $contact_id . '\n';
  $tag_id = get_tag_id($tag);

  $params = array(
    'contact_id' => $contact_id,
    'tag_id' => $tag_id,
  );

  $result = civicrm_api3('EntityTag', 'create', $params);
  return $result;
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
  //echo 'Deleting ' . $tag_id . ' from ' . $contact_id . '\n';
  $params = array(
    'contact_id_h' => $contact_id,
    'tag_id' => $tag_id,
  );
  $result = civicrm_api3('EntityTag', 'delete', $params);
  return $result;
}

function delete_tag_from_contacts($tag_id, $contacts) {
  foreach ($contacts as $contact_id => $contact_data) {
    delete_tag_from_contact ($tag_id, $contact_id);
  }
}

function get_tagged_contacts ($tag) {
  return civicrm_api3('Tag', 'get', array(
    'name' => $tag,
    'rowCount' => 0,
  ));
}

/* dead code
function delete_tag_from_all($tag) {
  $to_delete = civicrm_api3('Tag', 'get', array(
    'name' => $tag,
    'rowCount' => 0,
  ));

  // This foreach is expected to unpack and process a single result.
  foreach($to_delete['values'] as $value) { 
    if ($value['name'] == $tag) {
      $tag_id = $value['id'];
      $contacts = get_tagged_contacts($tag_id);
      delete_tag_from_contacts($tag_id, $contacts);
    }
  };
}
*/

/* dead code
function delete_tags($tag_map) {
  // No need to filter for sensible tags here, if the tag doesn't exist
  // the query will not return any contacts to process.
  foreach ($tag_map as $tag => $smart_group) {
    delete_tag($tag);
  }
}
*/

function apply_tags($tag_map) {
  $tally = array();
  foreach ($tag_map as $tag => $smart_group) {
    try {
      if (!(array_key_exists($tag, $tally))) {
        $tally[$tag] = 0;
      };
      $contacts = contact_get_smart_group($smart_group);
      if ($contacts != null) {
        foreach ($contacts as $contact_id => $contact) {
          add_tag_to_contact ($tag, $contact_id);
          $tally[$tag] += 1;
        }
      };
    }
    catch (CiviCRM_API3_Exception $e) {
      $error_message = 'Could not apply tag ' . $tag . ':  ' . $e->getMessage();
      log_message($error_message);
      CRM_Core_Session::setStatus(ts($error_message), ts('apply_tags failure in SmartTag.UpdateTags'), 'no-popup');
    }
  }
  return $tally;
}

function delete_and_apply_tags($tag_map) {
  $tally = array();
  foreach ($tag_map as $tag => $smart_group) {
    try {

      $tagged_contacts = get_tagged_contacts($tag);
      log_json($tagged_contacts);
      /*
      $sgroup_contacts = contact_get_smart_group ($smart_group);

      $contacts_to_delete_tag = subtract_list ($tagged_contacts, $sgroup_contacts);
      $contacts_to_add_tag = subtract_list ($sgroup_contacts, $tagged_contacts);

      delete_tag_from_contacts ($tag, $contacts_to_delete_tag);
      add_tag_to_contacts ($tag, $contacts_to_add_tag);
      */
/*
      delete_tag_from_all($tag);

      if (!(array_key_exists($tag, $tally))) {
        $tally[$tag] = 0;
      };
      $contacts = contact_get_smart_group($smart_group);
//      CRM_Core_Session::setStatus('Got contacts ', 'Success', 'no-popup');
      if ($contacts != null) {
        foreach ($contacts as $contact_id => $contact) {
          add_tag_to_contact ($tag, $contact_id);
//          CRM_Core_Session::setStatus('Tagged contact ' . $contact_id, 'Success', 'no-popup');
          $tally[$tag] += 1;
        }
      };
*/
    }
    catch (CiviCRM_API3_Exception $e) {
      $error_message = 'Could not apply tag ' . $tag . ':  ' . $e->getMessage();
      log_message($error_message);
      CRM_Core_Session::setStatus(ts($error_message), ts('apply_tags failure in SmartTag.UpdateTags'), 'no-popup');
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
    $tag_map = load_map("map.txt");
//    CRM_Core_Session::setStatus('Tag map loaded', 'Success', 'no-popup');
//    delete_tags($tag_map);
//    $tally = apply_tags($tag_map);
    $tally = delete_and_apply_tags($tag_map);
//    CRM_Core_Session::setStatus('Tag map processed', 'Success', 'no-popup');
    $endtime = time();
    $time_taken = $endtime - $starttime;
    $message = 'UpdateTags Success in ' . $time_taken . ' seconds: ' . json_encode($tally);
    log_message ($message);
    CRM_Core_Session::setStatus($message, 'Success', 'no-popup');
//    header("Refresh:0"); // FIXME I want to refresh the page to display status messages, but this does not work.
    return civicrm_api3_create_success($tally, $params, 'Smarttag', 'Updatetags');
  }
  catch (CiviCRM_API3_Exception $e) {
    // Handle error here.
    $error_message = $e->getMessage();
    return civicrm_api3_create_error($error_message);
  }
}

