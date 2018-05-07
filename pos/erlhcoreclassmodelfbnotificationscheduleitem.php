<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_notification_schedule_item";
$def->class = "erLhcoreClassModelFBNotificationScheduleItem";

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

$def->properties['lead_id'] = new ezcPersistentObjectProperty();
$def->properties['lead_id']->columnName   = 'lead_id';
$def->properties['lead_id']->propertyName = 'lead_id';
$def->properties['lead_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['campaign_id'] = new ezcPersistentObjectProperty();
$def->properties['campaign_id']->columnName   = 'campaign_id';
$def->properties['campaign_id']->propertyName = 'campaign_id';
$def->properties['campaign_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['send_time'] = new ezcPersistentObjectProperty();
$def->properties['send_time']->columnName   = 'send_time';
$def->properties['send_time']->propertyName = 'send_time';
$def->properties['send_time']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['log'] = new ezcPersistentObjectProperty();
$def->properties['log']->columnName   = 'log';
$def->properties['log']->propertyName = 'log';
$def->properties['log']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>