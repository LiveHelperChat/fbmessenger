<?php

// Works as proxy to child independent instances

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

use Tgallice\FBMessenger\WebhookRequestHandler;
use Tgallice\FBMessenger\Callback\MessageEvent;
use Tgallice\FBMessenger\Callback\PostbackEvent;
use Tgallice\FBMessenger\Callback\MessageEchoEvent;

$webookHandler = new WebhookRequestHandler($ext->settings['app_settings']['app_secret'], $ext->settings['app_settings']['verify_token']);

if (!$webookHandler->isValidCallbackRequest()) {
    if ($ext->settings['enable_debug'] == true) {
        erLhcoreClassLog::write('INVALID__TOKEN');
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


$events = $webookHandler->getAllCallbackEvents();

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

foreach($events as $event) {

    if ($event instanceof MessageEvent || $event instanceof PostbackEvent || $event instanceof MessageEchoEvent) {
        try {

            if ($event instanceof MessageEchoEvent) {
                $pageId = $event->getSenderId();
            } else {
                $pageId = $event->getRecipientId();
            }

            $stmt = $db->prepare("SELECT address FROM lhc_fbmessenger_standalone_fb_page WHERE page_id = :page_id");
            $stmt->bindValue(':page_id', $pageId);
            $stmt->execute();
            $addressInstance = $stmt->fetchColumn();

            if (!empty($addressInstance)) {
                erLhcoreClassFBValidator::proxyStandaloneRequest([
                    'headers' => [
                        'Facebook-API-Version: '.$_SERVER['HTTP_FACEBOOK_API_VERSION'],
                        'X-Hub-Signature: '.$_SERVER['HTTP_X_HUB_SIGNATURE']
                    ],
                    'body' => file_get_contents('php://input'),
                    'address' => 'https://'.$addressInstance.'/fbmessenger/callbackgeneral'
                ]);
            } else {
                throw new Exception('Page not found to proxy - ' . $pageId);
            }

        } catch (Exception $e) {
            if ($ext->settings['enable_debug'] == true) {
                erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
            }
        }
    }
}

exit();

?>