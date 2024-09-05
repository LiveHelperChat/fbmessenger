<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_lead";
$def->class = "erLhcoreClassModelFBLead";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['source'] = new ezcPersistentObjectProperty();
$def->properties['source']->columnName   = 'source';
$def->properties['source']->propertyName = 'source';
$def->properties['source']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['is_payment_enabled'] = new ezcPersistentObjectProperty();
$def->properties['is_payment_enabled']->columnName   = 'is_payment_enabled';
$def->properties['is_payment_enabled']->propertyName = 'is_payment_enabled';
$def->properties['is_payment_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['blocked'] = new ezcPersistentObjectProperty();
$def->properties['blocked']->columnName   = 'blocked';
$def->properties['blocked']->propertyName = 'blocked';
$def->properties['blocked']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

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

$def->properties['profile_pic'] = new ezcPersistentObjectProperty();
$def->properties['profile_pic']->columnName   = 'profile_pic';
$def->properties['profile_pic']->propertyName = 'profile_pic';
$def->properties['profile_pic']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['profile_pic_updated'] = new ezcPersistentObjectProperty();
$def->properties['profile_pic_updated']->columnName   = 'profile_pic_updated';
$def->properties['profile_pic_updated']->propertyName = 'profile_pic_updated';
$def->properties['profile_pic_updated']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['page_id'] = new ezcPersistentObjectProperty();
$def->properties['page_id']->columnName   = 'page_id';
$def->properties['page_id']->propertyName = 'page_id';
$def->properties['page_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['creator_id'] = new ezcPersistentObjectProperty();
$def->properties['creator_id']->columnName   = 'creator_id';
$def->properties['creator_id']->propertyName = 'creator_id';
$def->properties['creator_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['is_payment_enabled'] = new ezcPersistentObjectProperty();
$def->properties['is_payment_enabled']->columnName   = 'is_payment_enabled';
$def->properties['is_payment_enabled']->propertyName = 'is_payment_enabled';
$def->properties['is_payment_enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['blocked'] = new ezcPersistentObjectProperty();
$def->properties['blocked']->columnName   = 'blocked';
$def->properties['blocked']->propertyName = 'blocked';
$def->properties['blocked']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>