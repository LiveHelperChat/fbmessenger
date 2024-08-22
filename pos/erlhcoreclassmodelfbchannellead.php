<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "bot_channels_lead";
$def->class = "erLhcoreClassModelFBBChannelLead";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['channel_id'] = new ezcPersistentObjectProperty();
$def->properties['channel_id']->columnName   = 'channel_id';
$def->properties['channel_id']->propertyName = 'channel_id';
$def->properties['channel_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['lead_id'] = new ezcPersistentObjectProperty();
$def->properties['lead_id']->columnName   = 'lead_id';
$def->properties['lead_id']->propertyName = 'lead_id';
$def->properties['lead_id']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

return $def;

?>