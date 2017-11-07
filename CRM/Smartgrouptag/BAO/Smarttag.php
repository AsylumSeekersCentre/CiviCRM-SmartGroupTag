<?php

class CRM_Smartgrouptag_BAO_Smarttag extends CRM_Smartgrouptag_DAO_Smarttag {

  /**
   * Create a new Smarttag based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Smartgrouptag_DAO_Smarttag|NULL
   *
  public static function create($params) {
    $className = 'CRM_Smartgrouptag_DAO_Smarttag';
    $entityName = 'Smarttag';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
