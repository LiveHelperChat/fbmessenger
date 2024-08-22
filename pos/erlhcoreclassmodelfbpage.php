<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_page";
$def->class = "erLhcoreClassModelFBPage";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['dep_id'] = new ezcPersistentObjectProperty();
$def->properties['dep_id']->columnName   = 'dep_id';
$def->properties['dep_id']->propertyName = 'dep_id';
$def->properties['dep_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['page_token'] = new ezcPersistentObjectProperty();
$def->properties['page_token']->columnName   = 'page_token';
$def->properties['page_token']->propertyName = 'page_token';
$def->properties['page_token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['verify_token'] = new ezcPersistentObjectProperty();
$def->properties['verify_token']->columnName   = 'verify_token';
$def->properties['verify_token']->propertyName = 'verify_token';
$def->properties['verify_token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['app_secret'] = new ezcPersistentObjectProperty();
$def->properties['app_secret']->columnName   = 'app_secret';
$def->properties['app_secret']->propertyName = 'app_secret';
$def->properties['app_secret']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['verified'] = new ezcPersistentObjectProperty();
$def->properties['verified']->columnName   = 'verified';
$def->properties['verified']->propertyName = 'verified';
$def->properties['verified']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['bot_disabled'] = new ezcPersistentObjectProperty();
$def->properties['bot_disabled']->columnName   = 'bot_disabled';
$def->properties['bot_disabled']->propertyName = 'bot_disabled';
$def->properties['bot_disabled']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>