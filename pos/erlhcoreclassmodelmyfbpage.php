<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_my_page";
$def->class = "erLhcoreClassModelMyFBPage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['page_id'] = new ezcPersistentObjectProperty();
$def->properties['page_id']->columnName   = 'page_id';
$def->properties['page_id']->propertyName = 'page_id';
$def->properties['page_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['instagram_business_account'] = new ezcPersistentObjectProperty();
$def->properties['instagram_business_account']->columnName   = 'instagram_business_account';
$def->properties['instagram_business_account']->propertyName = 'instagram_business_account';
$def->properties['instagram_business_account']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['whatsapp_business_account_id'] = new ezcPersistentObjectProperty();
$def->properties['whatsapp_business_account_id']->columnName   = 'whatsapp_business_account_id';
$def->properties['whatsapp_business_account_id']->propertyName = 'whatsapp_business_account_id';
$def->properties['whatsapp_business_account_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['whatsapp_business_phone_number_id'] = new ezcPersistentObjectProperty();
$def->properties['whatsapp_business_phone_number_id']->columnName   = 'whatsapp_business_phone_number_id';
$def->properties['whatsapp_business_phone_number_id']->propertyName = 'whatsapp_business_phone_number_id';
$def->properties['whatsapp_business_phone_number_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['access_token'] = new ezcPersistentObjectProperty();
$def->properties['access_token']->columnName   = 'access_token';
$def->properties['access_token']->propertyName = 'access_token';
$def->properties['access_token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['enabled'] = new ezcPersistentObjectProperty();
$def->properties['enabled']->columnName   = 'enabled';
$def->properties['enabled']->propertyName = 'enabled';
$def->properties['enabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>