<?php

erLhcoreClassLog::write(file_get_contents("php://input"));

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
use Tgallice\FBMessenger\Callback\MessageEvent;
use Tgallice\FBMessenger\Callback\PostbackEvent;
use Tgallice\FBMessenger\Callback\MessageEchoEvent;
use Tgallice\FBMessenger\Callback\MessageDeleteEvent;

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
fastcgi_finish_request();

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$payloadData = json_decode(file_get_contents("php://input"),true);

if (isset($payloadData['entry']) && is_array($payloadData['entry'])) {
    foreach ($payloadData['entry'] as $entryData) {
        $db->query('USE ' . $cfg->getSetting('db', 'database'));

        $stmt = $db->prepare("SELECT instance_id FROM lhc_instance_fb_page WHERE whatsapp_business_account_id = :whatsapp_business_account_id");
        $stmt->bindValue(':whatsapp_business_account_id', $entryData['id']);
        $stmt->execute();
        $instanceId = $stmt->fetchColumn();

        if (is_numeric($instanceId)) {
            erLhcoreClassInstance::$instanceChat->id = $instanceId;
            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $instanceId);

            $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookwhatsappscope')));

            if (!is_object($webhookPresent)) {
                // Install dependencies with chosen department
                $subscribeNumber = erLhcoreClassModelMyFBPage::findOne(['filter' => ['whatsapp_business_account_id' => $entryData['id']]]);
                \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatActivator::installOrUpdate(['dep_id' => $subscribeNumber->dep_id]);
            }

            $identifier = $webhookPresent->identifier;
            break;
        }
    }
}

$Params['user_parameters']['identifier'] = $identifier;
include 'modules/lhwebhooks/incoming.php';

exit();
?>