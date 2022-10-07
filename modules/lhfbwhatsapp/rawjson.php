<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/rawjson.tpl.php');

$item = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::fetch($Params['user_parameters']['id']);
$tpl->set('item',$item);

echo $tpl->fetch();
exit;

?>