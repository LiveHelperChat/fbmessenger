<?php

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $ext->settings['app_settings']['instagram_verify_token']) {
    if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {

        if ($ext->settings['enable_debug'] == true) {
            erLhcoreClassLog::write('VERIFIED');
        }

        echo $_GET['hub_challenge'];
        exit;
    }
}

// Verify request
use Tgallice\FBMessenger\WebhookRequestHandler;

$webookHandler = new WebhookRequestHandler($ext->settings['app_settings']['app_secret'], $ext->settings['app_settings']['instagram_verify_token']);
if (!$webookHandler->isValidInstagramCallbackRequest()) {
    if ($ext->settings['enable_debug'] == true) {
        erLhcoreClassLog::write('INVALID__TOKEN' . print_r($ext->settings['app_settings'],true));
    }
    exit;
}

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookinstagramappscope')));
if (!is_object($webhookPresent)) {
    \LiveHelperChatExtension\fbmessenger\providers\FBMessengerInstagramAppLiveHelperChatActivator::installOrUpdate(['dep_id' => 0]);
    $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookinstagramappscope')));
}

$Params['user_parameters']['identifier'] = $webhookPresent->identifier;;
include 'modules/lhwebhooks/incoming.php';
exit();
?>