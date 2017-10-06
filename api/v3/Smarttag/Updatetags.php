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

function echo_json($v) {
  echo '<pre>' . json_encode($v) . '</pre>';
}

function get_group_id($group_name_untrimmed) {
  $group_name = ltrim(rtrim($group_name_untrimmed));
//  echo $group_name;
  $params = array(
    'sequential' => 1,
    'title' => $group_name,
  );
  $table = civicrm_api3('Group', 'get', $params);
//  echo_json($table);
  $result = null;
  foreach ($table['values'] as $mgroup) {
//    echo json_encode($mgroup);
    if ($mgroup['title'] == $group_name) {
      $result = $mgroup['id'];
    }
  };
  echo_json($result);
  return $result;

/* 
  try{
    $result = null;
    $table = civicrm_api3('Group', 'get', $params);
    echo "aaa\n" . json_encode($table);
    foreach ($table['values'] as $mgroup) {
      echo json_encode($mgroup);
      if ($mgroup['name'] == $group_name) {
        $result = $mgroup['id'];
      }
    };
    return $result;
  }
  catch (CiviCRM_API3_Exception $e) {
    // Handle error here.
    $errorMessage = $e->getMessage();
    $errorCode = $e->getErrorCode();
    $errorData = $e->getExtraParams();
    return array(
      'is_error' => 1,
      'error_message' => $errorMessage,
      'error_code' => $errorCode,
      'error_data' => $errorData,
    );
  }
  return $result;
*/
}

function contact_get_smart_group($group_name) {
//  echo $group_name . '\n';
  $group_id = get_group_id($group_name);
  if ($group_id == null) {
    echo 'Null group ' . $group_name;
//    throw new Exception('Non-existant group passed to contact_get_smart_group'); // FIXME if this line is added, the process will crash if there's a bad group name in the mapping file.
  }
  else {
// FIXME Leaving this here for now - the examples suggest this is the correct
// structure for this query, but the one used below is simpler and works.
// More investigation to follow.
//  $params = array(
//    'group' => array(
//      'IN' => $group_id, //array(
//        '0' => $group_id,
 //     ),
//    )
//  );
    $params = array();
    $params['group'] = $group_id;
   
    try{
      $result = civicrm_api3('Contact', 'get', $params);
      echo_json($result);
      return $result['values'];
    }
    catch (CiviCRM_API3_Exception $e) {
      // Handle error here.
      $errorMessage = $e->getMessage();
      $errorCode = $e->getErrorCode();
      $errorData = $e->getExtraParams();
      return array(
        'is_error' => 1,
        'error_message' => $errorMessage,
        'error_code' => $errorCode,
        'error_data' => $errorData,
      );
    }
  }
}

function delete_tag_from_contact($tag_id, $contact_id) {
//  echo "Deleting " . $tag_id . " from " . $contact_id . "\n";
  $params = array(
    'contact_id_h' => $contact_id,
    'tag_id' => $tag_id,
  );
  try{
    $result = civicrm_api3('EntityTag', 'delete', $params);
  }
  catch (CiviCRM_API3_Exception $e) {
    // Handle error here.
    $errorMessage = $e->getMessage();
    $errorCode = $e->getErrorCode();
    $errorData = $e->getExtraParams();
    return array(
      'is_error' => 1,
      'error_message' => $errorMessage,
      'error_code' => $errorCode,
      'error_data' => $errorData,
    );
  }
  return $result;
}

function delete_tag_from_contacts($tag_id, $contacts) {
  foreach ($contacts as $contact_id => $contact_data) {
    delete_tag_from_contact ($tag_id, $contact_id);
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
  try{
    $result = civicrm_api3('EntityTag', 'create', $params);
  }
  catch (CiviCRM_API3_Exception $e) {
    // Handle error here.
    $errorMessage = $e->getMessage();
    $errorCode = $e->getErrorCode();
    $errorData = $e->getExtraParams();
    return array(
      'error' => $errorMessage,
      'error_code' => $errorCode,
      'error_data' => $errorData,
    );
  }
  return $result;
}

function split_file_strings($strings) {
  $result = array();
  try {
    foreach ($strings as $string) {
      $pair = explode(',', $string);
      $result[$pair[0]] = $pair[1];
    };
  } catch (Exception $e) {
    echo '<pre>Caught exception: ',  $e->getMessage(), "\n</pre>"; // FIXME
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

function delete_tags($tag_map) {
  foreach ($tag_map as $tag => $smart_group) {
    $extant = civicrm_api3('Tag', 'get', array(
      'name' => $tag,
    ));

    // workaround to unpack result from return format
    foreach($extant['values'] as $value) { 
      if ($value['name'] == $tag) {
        $id = $value['id'];
        $contacts = get_tagged_contacts($id);
        delete_tag_from_contacts($id, $contacts);
      }
    };
  }
}

function apply_tags($tag_map) {
  foreach ($tag_map as $tag => $smart_group) {
    $contacts = contact_get_smart_group($smart_group);
    foreach ($contacts as $contact_id => $contact) {
      add_tag_to_contact ($tag, $contact_id);
    }
//    echo '<pre>Contacts:\n' . json_encode($contacts) . '</pre>';
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
  $tag_map = load_map("map.txt");
  delete_tags($tag_map);
  apply_tags($tag_map);
  return civicrm_api3_create_success($example_result, $params, 'Smarttag', 'Updatetags');
}

