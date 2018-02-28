<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_fbuser";
$def->class = "erLhcoreClassModelFBMessengerUser";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['fb_user_id'] = new ezcPersistentObjectProperty();
$def->properties['fb_user_id']->columnName   = 'fb_user_id';
$def->properties['fb_user_id']->propertyName = 'fb_user_id';
$def->properties['fb_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['access_token'] = new ezcPersistentObjectProperty();
$def->properties['access_token']->columnName   = 'access_token';
$def->properties['access_token']->propertyName = 'access_token';
$def->properties['access_token']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>