<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_notification_schedule_campaign";
$def->class = "erLhcoreClassModelFBNotificationScheduleCampaign";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['schedule_id'] = new ezcPersistentObjectProperty();
$def->properties['schedule_id']->columnName   = 'schedule_id';
$def->properties['schedule_id']->propertyName = 'schedule_id';
$def->properties['schedule_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['status'] = new ezcPersistentObjectProperty();
$def->properties['status']->columnName   = 'status';
$def->properties['status']->propertyName = 'status';
$def->properties['status']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_id'] = new ezcPersistentObjectProperty();
$def->properties['last_id']->columnName   = 'last_id';
$def->properties['last_id']->propertyName = 'last_id';
$def->properties['last_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['ctime'] = new ezcPersistentObjectProperty();
$def->properties['ctime']->columnName   = 'ctime';
$def->properties['ctime']->propertyName = 'ctime';
$def->properties['ctime']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['last_send'] = new ezcPersistentObjectProperty();
$def->properties['last_send']->columnName   = 'last_send';
$def->properties['last_send']->propertyName = 'last_send';
$def->properties['last_send']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>