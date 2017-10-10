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

/*
function log_message($title, $message) {
//  CRM_Core_error::debug($title, $message)
//  CRM_Core_Session::setStatus($error_message, 'contact_get_smart_group failure in SmartTag.UpdateTags', 'no-popup');
}
*/

function echo_json($v) {
  echo '<pre>' . json_encode($v) . '</pre>';
}

function get_group_id($group_name) {
  $params = array(
    'sequential' => 1,
    'title' => $group_name,
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
  if ($group_id == null) {
    $error_message = 'No group named ' . $group_name;
//    echo $error_message;
//    log_message($error_message, $error_message);
    CRM_Core_Session::setStatus(ts($error_message), ts('contact_get_smart_group failure in SmartTag.UpdateTags'), 'no-popup');
// If the next line is added, the process will crash if there's a bad group name in the mapping file.
//    throw new Exception('Non-existant group passed to contact_get_smart_group'); 
  }
  else {
   $params = array(
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
   
    $result = civicrm_api3('Contact', 'get', $params);
    return $result['values'];
  }
}

function get_tag_id ($tag) {
  $data = civicrm_api3('Tag', 'get', array(
    'sequential' => 1,
      'name' => $tag,
  ));
  return $data['id'];
}

function add_tag_to_contact ($tag, $contact_id) {
  echo 'Adding tag ' . $tag . ' to contact ' . $contact_id . '\n';
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

function delete_tags($tag_map) {
  // No need to filter for sensible tags here, if the tag doesn't exist
  // the query will not return any contacts to process.
  foreach ($tag_map as $tag => $smart_group) {
    $to_delete = civicrm_api3('Tag', 'get', array(
      'name' => $tag,
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
}

function apply_tags($tag_map) {
  // trimming smart_group_untrimmed should no longer be necessary - these 
  // strings are now trimmed upon loading from file. To fix and check. FIXME
  foreach ($tag_map as $tag => $smart_group_untrimmed) {
    try {
      $smart_group = ltrim(rtrim($smart_group_untrimmed));
      $contacts = contact_get_smart_group($smart_group);
      foreach ($contacts as $contact_id => $contact) {
        add_tag_to_contact ($tag, $contact_id);
      }
    }
    catch (CiviCRM_API3_Exception $e) {
      $error_message = 'Could not apply tag ' . $tag . ':  ' . $e->getMessage();
      echo $error_message;
      CRM_Core_Session::setStatus(ts($error_message), ts('apply_tags failure in SmartTag.UpdateTags'), 'no-popup');
//      log_message($error_message, 'apply_tags failure in SmartTag.UpdateTags')
    }
  }
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
//  civicrm_api3_status('This is a message', '');
    $tag_map = load_map("map.txt");
    delete_tags($tag_map);
    apply_tags($tag_map);
    return civicrm_api3_create_success(array(), $params, 'Smarttag', 'Updatetags');
  }
  catch (CiviCRM_API3_Exception $e) {
    // Handle error here.
    $error_message = $e->getMessage();
    return civicrm_api3_create_error($error_message);
  }
}

