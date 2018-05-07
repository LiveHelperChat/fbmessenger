<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_notification_schedule";
$def->class = "erLhcoreClassModelFBNotificationSchedule";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['filter'] = new ezcPersistentObjectProperty();
$def->properties['filter']->columnName   = 'filter';
$def->properties['filter']->propertyName = 'filter';
$def->properties['filter']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['message'] = new ezcPersistentObjectProperty();
$def->properties['message']->columnName   = 'message';
$def->properties['message']->propertyName = 'message';
$def->properties['message']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['start_at'] = new ezcPersistentObjectProperty();
$def->properties['start_at']->columnName   = 'start_at';
$def->properties['start_at']->propertyName = 'start_at';
$def->properties['start_at']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_send'] = new ezcPersistentObjectProperty();
$def->properties['last_send']->columnName   = 'last_send';
$def->properties['last_send']->propertyName = 'last_send';
$def->properties['last_send']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['interval'] = new ezcPersistentObjectProperty();
$def->properties['interval']->columnName   = 'interval';
$def->properties['interval']->propertyName = 'interval';
$def->properties['interval']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['amount'] = new ezcPersistentObjectProperty();
$def->properties['amount']->columnName   = 'amount';
$def->properties['amount']->propertyName = 'amount';
$def->properties['amount']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>