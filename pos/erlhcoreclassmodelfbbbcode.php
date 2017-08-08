<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessenger_bbcode";
$def->class = "erLhcoreClassModelFBBBCode";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['bbcode'] = new ezcPersistentObjectProperty();
$def->properties['bbcode']->columnName   = 'bbcode';
$def->properties['bbcode']->propertyName = 'bbcode';
$def->properties['bbcode']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['configuration'] = new ezcPersistentObjectProperty();
$def->properties['configuration']->columnName   = 'configuration';
$def->properties['configuration']->propertyName = 'configuration';
$def->properties['configuration']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;

?>