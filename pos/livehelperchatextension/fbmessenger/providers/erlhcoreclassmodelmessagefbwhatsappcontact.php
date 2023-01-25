<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessengerwhatsapp_contact";
$def->class = '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array(
             'email','data','name','phone','phone_recipient',
             'attr_str_1','attr_str_2','attr_str_3',
             'attr_str_4','attr_str_5','attr_str_6',
             'title','lastname','company','file_1','file_2','file_3','file_4'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (array(
             'disabled',
             'created_at',
             'date','delivery_status',
             'chat_id','user_id','private'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;


?>