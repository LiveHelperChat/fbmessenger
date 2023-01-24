<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lhc_fbmessengerwhatsapp_campaign";
$def->class = '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign';

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

foreach (array('name','message_variables','phone_sender','phone_sender_id','template','template_id','language') as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (array(
            'status','starts_at','user_id','enabled',
             'business_account_id','dep_id','private'
         ) as $attr) {
    $def->properties[$attr] = new ezcPersistentObjectProperty();
    $def->properties[$attr]->columnName   = $attr;
    $def->properties[$attr]->propertyName = $attr;
    $def->properties[$attr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>
?>