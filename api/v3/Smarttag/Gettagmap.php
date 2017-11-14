<?php
use CRM_Smartgrouptag_ExtensionUtil as E;

/**
 * Smarttag.GetTagMap API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_smarttag_Gettagmap_spec(&$spec) {
// There are no required arguments for this.
//  $spec['magicword']['api.required'] = 1;
}


/**
 * Smarttag.GetTagMap API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_smarttag_Gettagmap($params) {

  try {

    $contactParams = array(
      'sequential' => 1,
      'version' => 3,
//      'tag'=> $tag_id,
      'rowCount' => 0,
    );

    $returnValues = civicrm_api3("Smarttag", "get", $contactParams);
//    $returnValues = civicrm_api3_smarttag_get ($contactParams);

    return civicrm_api3_create_success($returnValues, $params, 'NewEntity', 'NewAction');

  }

  catch (CiviCRM_API3_Exception $e) {

    // Handle errors here.
    $error_message = $e->getMessage();
    return civicrm_api3_create_error($error_message);

  }
}
