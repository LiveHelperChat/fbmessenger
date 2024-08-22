<?php

erLhcoreClassLog::write(file_get_contents("php://input"));

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
        erLhcoreClassLog::write('INVALID__TOKEN' . print_r($ext->settings['app_settings'],true));
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

$events = $webookHandler->getAllCallbackEvents();



foreach ($events as $event) {

    if ($event instanceof MessageEvent || $event instanceof PostbackEvent || $event instanceof MessageEchoEvent) {
        try {
            if ($ext->settings['enable_debug'] == true) {
                if ($event instanceof MessageEvent) {
                    erLhcoreClassLog::write('Message HERE - ' . $event->getMessageText());
                }
                erLhcoreClassLog::write('Sender User Id - HERE ' . $event->getSenderId());
            }

            // Page ID
            if ($event instanceof MessageEchoEvent) {
                $pageId = $event->getSenderId();
            } else {
                $pageId = $event->getRecipientId();
            }

            $db->query('USE ' . $cfg->getSetting('db', 'database'));

            $stmt = $db->prepare("SELECT instance_id FROM lhc_instance_fb_page WHERE page_id = :page_id");
            $stmt->bindValue(':page_id', $pageId);
            $stmt->execute();
            $instanceId = $stmt->fetchColumn();

            erLhcoreClassInstance::$instanceChat->id = $instanceId;

            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $instanceId);

            $page = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('page_id' => $pageId)));

            if ($page instanceof erLhcoreClassModelMyFBPage) {

                $ext->setPage($page);

                if ($event instanceof MessageEvent) {
                    $ext->processVisitorMessage($event);
                } elseif ($event instanceof PostbackEvent) {
                    $ext->processCallbackDefault($event);
                } elseif ($event instanceof MessageEchoEvent) {
                    $ext->processEchoMessage($event);
                }

                $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
                $dispatcher->dispatch('fbmessenger.messageprocessed', array('page' => $page));

            } else {
                throw new Exception('Facebook page could not be found!');
            }

        } catch (Exception $e) {
            if ($ext->settings['enable_debug'] == true) {
                erLhcoreClassLog::write(print_r($e->getMessage(), true)) . "\n";
            }
        }
    } else {
        //erLhcoreClassLog::write('UNKOWN EVENT ' . get_class($event));
    }
}

exit();
?>