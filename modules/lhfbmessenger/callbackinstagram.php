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

// Just for review
/*$webookHandler = new WebhookRequestHandler($ext->settings['app_settings']['app_secret'], $ext->settings['app_settings']['instagram_verify_token']);

if (!$webookHandler->isValidInstagramCallbackRequest()) {
    if ($ext->settings['enable_debug'] == true) {
        erLhcoreClassLog::write('INVALID__TOKEN' . print_r($ext->settings['app_settings'],true));
    }
    exit;
}*/

/*ob_start();
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

$events = $webookHandler->getAllCallbackEvents();*/

/*foreach ($events as $event) {

    erLhcoreClassLog::write('CLASS ' . get_class($event));

    if ($event instanceof MessageEvent || $event instanceof PostbackEvent || $event instanceof MessageEchoEvent || $event instanceof MessageDeleteEvent) {
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

            $stmt = $db->prepare("SELECT instance_id FROM lhc_instance_fb_page WHERE instagram_business_account = :page_id");
            $stmt->bindValue(':page_id', $pageId);
            $stmt->execute();
            $instanceId = $stmt->fetchColumn();

            erLhcoreClassInstance::$instanceChat->id = $instanceId;

            $db->query('USE ' . $cfg->getSetting('db', 'database_user_prefix') . $instanceId);

            $page = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('instagram_business_account' => $pageId)));

            if ($page instanceof erLhcoreClassModelMyFBPage) {

                $ext->setPage($page);

                if ($event instanceof MessageEvent) {

                    $message = $event->getMessage();

                    if ($message->hasReply()) {
                        erLhcoreClassLog::write('IGNORE STORY REPLY' );
                        // Ignore story mentions messages
                        exit;
                    }

                    if ($message->hasAttachments()) {
                        $attatchements = $message->getAttachments();
                        foreach ($attatchements as $data) {
                            if ($data['type'] == 'story_mention') {
                                erLhcoreClassLog::write('IGNORE STORY MENTION' );
                                // Ignore story mentions messages
                                exit;
                            }

                            if ($data['type'] == 'share') {
                                erLhcoreClassLog::write('IGNORE STORY SHARE' );
                                // Ignore story mentions messages
                                exit;
                            }

                            if ($data['type'] == 'unsupported_type') {
                                erLhcoreClassLog::write('UNSUPPORTED TYPE' );
                                // Ignore story mentions messages
                                exit;
                            }
                        }
                    }

                   $ext->processVisitorMessage($event,'instagram');


                } elseif ($event instanceof MessageDeleteEvent) {
                    $ext->processVisitorDelete($event,'instagram');
                } elseif ($event instanceof PostbackEvent) {
                    //$ext->processCallbackDefault($event);
                } elseif ($event instanceof MessageEchoEvent) {
                    //$ext->processEchoMessage($event);
                }

                $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
                $dispatcher->dispatch('fbmessenger.messageprocessed', array('page' => $page));

            } else {
                throw new Exception('Facebook page could not be found!');
            }

        } catch (Exception $e) {
            //if ($ext->settings['enable_debug'] == true) {
                erLhcoreClassLog::write(print_r($e->getMessage(), true)) . "\n";
            //}
        }
    } else {
        erLhcoreClassLog::write('UNKOWN EVENT ' . get_class($event));
    }
}*/

exit();
?>