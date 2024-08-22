<?php

erLhcoreClassLog::write(file_get_contents("php://input"));

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
/*
 * {
  "object": "instagram",
  "entry": [
    {
      "time": 1649146383189,
      "id": "17841406424087811",
      "messaging": [
        {
          "sender": {
            "id": "4765048090283896"
          },
          "recipient": {
            "id": "17841406424087811"
          },
          "timestamp": 1649146382475,
          "message": {
            "mid": "aWdfZAG1faXRlbToxOklHTWVzc2FnZAUlEOjE3ODQxNDA2NDI0MDg3ODExOjM0MDI4MjM2Njg0MTcxMDMwMDk0OTEyODIxMzM0MDU3OTE4MTMwNDozMDQyMTM4MTI1NzYwMDc4Njc1Njk1ODgyNzkwNjU5Njg2NAZDZD",
            "text": "Developer test 2"
          }
        }
      ]
    }
  ]
}
{
  "object": "page",
  "entry": [
    {
      "id": "161532587221126",
      "time": 1649068938485,
      "messaging": [
        {
          "sender": {
            "id": "4495198570581026"
          },
          "recipient": {
            "id": "161532587221126"
          },
          "timestamp": 1649068938195,
          "message": {
            "mid": "m_ZKYx3kFkGQxwvcINle7dbk0dkawYkg9FOLBOuMAfCrmPzSqTuAmc7camDtfIl6sOR_jMIvJ4PZPIOO5DRQ0S3Q",
            "text": "I would like to know if you have travel package from SG to Nongsa Resort includes accomodations, rt ferry and land transfer"
          }
        }
      ]
    }
  ]
}
 * */

// curl -X GET "https://graph.facebook.com/v12.0/4765048090283896?fields=name,profile_pic,follower_count&access_token=EAAEIZBhN1GU0BAFXWga2sE9L9xfd3fotqSNTCTNvunafxGlkdG5aqjkIW3vAHh5gO4AI7kbHZBi0YblZArSx4VSweCuWhgUCl6ztPHI3nxsva3PLC1XoRpnrPk2nzMvZALAFNcWXvWUk3pHN5iU1yBgbcArzftdPi3hJ6GoJYAWdjXHqlCcG"

use Tgallice\FBMessenger\WebhookRequestHandler;
use Tgallice\FBMessenger\Callback\MessageEvent;
use Tgallice\FBMessenger\Callback\PostbackEvent;
use Tgallice\FBMessenger\Callback\MessageEchoEvent;
use Tgallice\FBMessenger\Callback\MessageDeleteEvent;

$webookHandler = new WebhookRequestHandler($ext->settings['app_settings']['app_secret'], $ext->settings['app_settings']['instagram_verify_token']);

if (!$webookHandler->isValidInstagramCallbackRequest()) {
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

                    // {"name":"Remigijus Kiminas","profile_pic":"https:\/\/scontent-yyz1-1.cdninstagram.com\/v\/t51.2885-15\/77414108_904826126579672_8021554666045177856_n.jpg?stp=dst-jpg_s200x200&_nc_cat=104&ccb=1-5&_nc_sid=8ae9d6&_nc_ohc=G0DdGWIryZMAX8b04Zi&_nc_ht=scontent-yyz1-1.cdninstagram.com&edm=ALmAK4EEAAAA&oh=00_AT8Tw_xS0JhbnagQVqhUD7fiz4JG7gGNRL8Rzmo_9AwOZA&oe=62507B14","follower_count":7,"id":"4765048090283896"}
                    // curl -X GET "https://graph.facebook.com/v12.0/4765048090283896?fields=name,profile_pic,follower_count&access_token=EAAEIZBhN1GU0BAFXWga2sE9L9xfd3fotqSNTCTNvunafxGlkdG5aqjkIW3vAHh5gO4AI7kbHZBi0YblZArSx4VSweCuWhgUCl6ztPHI3nxsva3PLC1XoRpnrPk2nzMvZALAFNcWXvWUk3pHN5iU1yBgbcArzftdPi3hJ6GoJYAWdjXHqlCcG"

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
}

exit();
?>