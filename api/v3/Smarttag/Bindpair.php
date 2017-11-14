<?php
use CRM_Smartgrouptag_ExtensionUtil as E;

/**
 * Smarttag.Bindpair API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_smarttag_Bindpair_spec(&$spec) {
}

/**
 * Smarttag.Bindpair API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_smarttag_Bindpair($params) {
  $tag_id = $params['tagId'];
  $group_id = $params['groupId'];
  $q_params = [];
  $q_params['tag_id'] = $tag_id;
  $q_params['group_id'] = $group_id;
  civicrm_api3('Smarttag', 'create', $q_params);
  
}
