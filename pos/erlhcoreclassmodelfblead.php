<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "bot_leads";
$def->class = "erLhcoreClassModelFBLead";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['instance_id'] = new ezcPersistentObjectProperty();
$def->properties['instance_id']->columnName   = 'instance_id';
$def->properties['instance_id']->propertyName = 'instance_id';
$def->properties['instance_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['creator_id'] = new ezcPersistentObjectProperty();
$def->properties['creator_id']->columnName   = 'creator_id';
$def->properties['creator_id']->propertyName = 'creator_id';
$def->properties['creator_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['page_id'] = new ezcPersistentObjectProperty();
$def->properties['page_id']->columnName   = 'page_id';
$def->properties['page_id']->propertyName = 'page_id';
$def->properties['page_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['profile_pic_updated'] = new ezcPersistentObjectProperty();
$def->properties['profile_pic_updated']->columnName   = 'profile_pic_updated';
$def->properties['profile_pic_updated']->propertyName = 'profile_pic_updated';
$def->properties['profile_pic_updated']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['creator_id'] = new ezcPersistentObjectProperty();
$def->properties['creator_id']->columnName   = 'creator_id';
$def->properties['creator_id']->propertyName = 'creator_id';
$def->properties['creator_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['source'] = new ezcPersistentObjectProperty();
$def->properties['source']->columnName   = 'source';
$def->properties['source']->propertyName = 'source';
$def->properties['source']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['first_name'] = new ezcPersistentObjectProperty();
$def->properties['first_name']->columnName   = 'first_name';
$def->properties['first_name']->propertyName = 'first_name';
$def->properties['first_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['last_name'] = new ezcPersistentObjectProperty();
$def->properties['last_name']->columnName   = 'last_name';
$def->properties['last_name']->propertyName = 'last_name';
$def->properties['last_name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['locale'] = new ezcPersistentObjectProperty();
$def->properties['locale']->columnName   = 'locale';
$def->properties['locale']->propertyName = 'locale';
$def->properties['locale']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['timezone'] = new ezcPersistentObjectProperty();
$def->properties['timezone']->columnName   = 'timezone';
$def->properties['timezone']->propertyName = 'timezone';
$def->properties['timezone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['gender'] = new ezcPersistentObjectProperty();
$def->properties['gender']->columnName   = 'gender';
$def->properties['gender']->propertyName = 'gender';
$def->properties['gender']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['email'] = new ezcPersistentObjectProperty();
$def->properties['email']->columnName   = 'email';
$def->properties['email']->propertyName = 'email';
$def->properties['email']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['phone'] = new ezcPersistentObjectProperty();
$def->properties['phone']->columnName   = 'phone';
$def->properties['phone']->propertyName = 'phone';
$def->properties['phone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['country'] = new ezcPersistentObjectProperty();
$def->properties['country']->columnName   = 'country';
$def->properties['country']->propertyName = 'country';
$def->properties['country']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['location'] = new ezcPersistentObjectProperty();
$def->properties['location']->columnName   = 'location';
$def->properties['location']->propertyName = 'location';
$def->properties['location']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['timezone'] = new ezcPersistentObjectProperty();
$def->properties['timezone']->columnName   = 'timezone';
$def->properties['timezone']->propertyName = 'timezone';
$def->properties['timezone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['gender'] = new ezcPersistentObjectProperty();
$def->properties['gender']->columnName   = 'gender';
$def->properties['gender']->propertyName = 'gender';
$def->properties['gender']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['profile_pic'] = new ezcPersistentObjectProperty();
$def->properties['profile_pic']->columnName   = 'profile_pic';
$def->properties['profile_pic']->propertyName = 'profile_pic';
$def->properties['profile_pic']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['phone'] = new ezcPersistentObjectProperty();
$def->properties['phone']->columnName   = 'phone';
$def->properties['phone']->propertyName = 'phone';
$def->properties['phone']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['_wait'] = new ezcPersistentObjectProperty();
$def->properties['_wait']->columnName   = '_wait';
$def->properties['_wait']->propertyName = '_wait';
$def->properties['_wait']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['_quick_save'] = new ezcPersistentObjectProperty();
$def->properties['_quick_save']->columnName   = '_quick_save';
$def->properties['_quick_save']->propertyName = '_quick_save';
$def->properties['_quick_save']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['linked_account'] = new ezcPersistentObjectProperty();
$def->properties['linked_account']->columnName   = 'linked_account';
$def->properties['linked_account']->propertyName = 'linked_account';
$def->properties['linked_account']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['subscribe'] = new ezcPersistentObjectProperty();
$def->properties['subscribe']->columnName   = 'subscribe';
$def->properties['subscribe']->propertyName = 'subscribe';
$def->properties['subscribe']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['is_payment_enabled'] = new ezcPersistentObjectProperty();
$def->properties['is_payment_enabled']->columnName   = 'is_payment_enabled';
$def->properties['is_payment_enabled']->propertyName = 'is_payment_enabled';
$def->properties['is_payment_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['auto_stop'] = new ezcPersistentObjectProperty();
$def->properties['auto_stop']->columnName   = 'auto_stop';
$def->properties['auto_stop']->propertyName = 'auto_stop';
$def->properties['auto_stop']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['created_at'] = new ezcPersistentObjectProperty();
$def->properties['created_at']->columnName   = 'created_at';
$def->properties['created_at']->propertyName = 'created_at';
$def->properties['created_at']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['updated_at'] = new ezcPersistentObjectProperty();
$def->properties['updated_at']->columnName   = 'updated_at';
$def->properties['updated_at']->propertyName = 'updated_at';
$def->properties['updated_at']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['deleted_at'] = new ezcPersistentObjectProperty();
$def->properties['deleted_at']->columnName   = 'deleted_at';
$def->properties['deleted_at']->propertyName = 'deleted_at';
$def->properties['deleted_at']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>