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



function entity_tag_delete_example() {
  $params = array(
    'contact_id_h' => 3161,
    'tag_id' => '7',
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

function entity_tag_create_example() {
  $params = array(
    'contact_id' => 3161, // contact: Jude Hungerford
    'tag_id' => 7, // Major Donor
//    'name' => 'Default',
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

function delete_tags($tag_map) {
  foreach ($tag_map as $tag => $smart_group) {
    $extant = civicrm_api3('Tag', 'get', array(
      'name' => $tag,
    ));
    echo 'tags = ' . json_encode($extant['values']) . '\n';
//    civicrm_api3
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
//  $example_result = entity_tag_delete_example();
  $tag_map = load_map("map.txt");
  echo "<pre>" . json_encode($tag_map) . "</pre>\n";
  delete_tags($tag_map);
  return civicrm_api3_create_success($example_result, $params, 'Smarttag', 'Updatetags');
/*
  if (array_key_exists('magicword', $params) && $params['magicword'] == 'sesame') {
    $returnValues = array(
      // OK, return several data rows
      12 => array('id' => 12, 'name' => 'Twelve'),
      34 => array('id' => 34, 'name' => 'Thirty four'),
      56 => array('id' => 56, 'name' => 'Fifty six'),
    );
*/
    // ALTERNATIVE: $returnValues = array(); // OK, success
    // ALTERNATIVE: $returnValues = array("Some value"); // OK, return a single value

    // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
//    return civicrm_api3_create_success($returnValues, $params, 'NewEntity', 'NewAction');
//  }
//  else {
//    throw new API_Exception(/*errorMessage*/ 'Everyone knows that the magicword is "sesame"', /*errorCode*/ 1234);
//  }
}
