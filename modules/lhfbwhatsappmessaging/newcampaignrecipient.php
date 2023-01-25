<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/newcampaignrecipient.tpl.php');

$campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($Params['user_parameters']['id']);

if (!($campaign instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign)) {
    die('Invalid campaign!');
}

if (is_numeric($Params['user_parameters']['recipient_id']) && $Params['user_parameters']['recipient_id'] > 0) {
    $item = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch($Params['user_parameters']['recipient_id']);
} else {
    $item = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient();
    $item->campaign_id = $campaign->id;
    $item->type = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::TYPE_MANUAL;
}

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    $items = array();
    $Errors = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::validateCampaignRecipient($item);
    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }
    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);
$tpl->set('campaign', $campaign);
echo $tpl->fetch();
exit;

?>