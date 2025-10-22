<?php

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $ext->settings['app_settings']['whatsapp_verify_token']) {
    if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {

        if ($ext->settings['enable_debug'] == true) {
            erLhcoreClassLog::write('VERIFIED WHATSAPP');
        }

        echo $_GET['hub_challenge'];
        exit;
    }
}

use Tgallice\FBMessenger\WebhookRequestHandler;

$webookHandler = new WebhookRequestHandler($ext->settings['app_settings']['app_secret'], $ext->settings['app_settings']['whatsapp_verify_token']);

if (!$webookHandler->isValidWhatsAppCallbackRequest()) {
    if ($ext->settings['enable_debug'] == true) {
        erLhcoreClassLog::write('INVALID__TOKEN_WHATSAPP' . print_r($ext->settings['app_settings'],true));
    }
    exit;
}

ob_start();
// do initial processing here
echo "ok";
header("HTTP/1.1 200 OK");
header('Connection: close');
header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();
if(session_id()) session_write_close();

if (function_exists('fastcgi_finish_request')){
    fastcgi_finish_request();
}

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$payloadData = json_decode(file_get_contents("php://input"),true);

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookwhatsappscope')));

if (!is_object($webhookPresent)) {
    \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatActivator::installOrUpdate(['dep_id' => 0]);
    $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookwhatsappscope')));
}

$Params['user_parameters']['identifier'] = $webhookPresent->identifier;;
include 'modules/lhwebhooks/incoming.php';

exit();
?>