<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/detailssend.tpl.php');

$recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch($Params['user_parameters']['id']);

if (!($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient)) {
    die('Invalid recipient!');
}

$tpl->set('item', $recipient);

echo $tpl->fetch();
exit;

?>