<?php

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $ext->settings['app_settings']['verify_token']) {
    if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {

        if ($ext->settings['enable_debug'] == true) {
            erLhcoreClassLog::write('VERIFIED');
        }

        echo $_GET['hub_challenge'];
        exit;
    }
}

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$dummyPayload = $payloadData = json_decode(file_get_contents("php://input"),true);

if (isset($payloadData['entry']) && is_array($payloadData['entry'])) {
    foreach ($payloadData['entry'] as $entryData) {
        $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookmessengerappscope')));
        if (!is_object($webhookPresent)) {
            // Install dependencies with chosen department
            $subscribeNumber = erLhcoreClassModelMyFBPage::findOne(['filter' => ['page_id' => $entryData['id']]]);
            if (is_object($subscribeNumber)){
                \LiveHelperChatExtension\fbmessenger\providers\FBMessengerMessengerAppLiveHelperChatActivator::installOrUpdate(['dep_id' => $subscribeNumber->dep_id]);
                $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookmessengerappscope')));
            }
        }
        $identifier = $webhookPresent->identifier;
        break;
    }
}

$Params['user_parameters']['identifier'] = $identifier;
include 'modules/lhwebhooks/incoming.php';

?>