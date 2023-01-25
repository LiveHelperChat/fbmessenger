<?php

session_write_close();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch($Params['user_parameters']['id']);

if (!($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient)) {
    die('Invalid recipient!');
}

$campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($recipient->campaign_id);

$instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

if ($campaign->business_account_id > 0) {
    $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($campaign->business_account_id);
    $instance->setAccessToken($account->access_token);
    $instance->setBusinessAccountID($account->business_account_id);
}

$templates = $instance->getTemplates();
$phones = $instance->getPhones();

\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingWorker::sendMessage($recipient, $campaign, $instance, $templates, $phones);

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>