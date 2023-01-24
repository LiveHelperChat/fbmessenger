<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessengerwhatsapp_contact_list_contact";
$def->class = '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactListContact';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
             'contact_list_id','contact_id'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>