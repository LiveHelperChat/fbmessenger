<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_chat";
$def->class = "erLhcoreClassModelFBChat";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['user_id'] = new ezcPersistentObjectProperty();
$def->properties['user_id']->columnName   = 'user_id';
$def->properties['user_id']->propertyName = 'user_id';
$def->properties['user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['chat_id'] = new ezcPersistentObjectProperty();
$def->properties['chat_id']->columnName   = 'chat_id';
$def->properties['chat_id']->propertyName = 'chat_id';
$def->properties['chat_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['recipient_user_id'] = new ezcPersistentObjectProperty();
$def->properties['recipient_user_id']->columnName   = 'recipient_user_id';
$def->properties['recipient_user_id']->propertyName = 'recipient_user_id';
$def->properties['recipient_user_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>