<?php 

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

// Load for which page callback is processed
$fbpage = erLhcoreClassModelFBPage::fetch($Params['user_parameters']['id']);

// Set page with which we are working
$ext->setPage($fbpage);

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $ext->getPage()->verify_token) {	
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

$webookHandler = new WebhookRequestHandler($ext->getPage()->app_secret, $ext->getPage()->verify_token);

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
fastcgi_finish_request();

$events = $webookHandler->getAllCallbackEvents();

foreach($events as $event) {

    if ($event instanceof MessageEvent || $event instanceof PostbackEvent || $event instanceof MessageEchoEvent) {

        if ($event instanceof MessageEchoEvent) {
            $pageId = $event->getSenderId();
        } else {
            $pageId = $event->getRecipientId();
        }

        if ($ext->settings['enable_debug'] == true) {
    		erLhcoreClassLog::write('Message - ' . $event->getMessageText());
    		erLhcoreClassLog::write('Sender User Id - ' . $event->getSenderId());
        }

	    try {
            if ($event instanceof MessageEvent) {
                $ext->processVisitorMessage($event);
            } elseif ($event instanceof PostbackEvent) {
                $ext->processCallbackDefault($event);
            } elseif ($event instanceof MessageEchoEvent) {
                $ext->processEchoMessage($event);
            }
		} catch (Exception $e) {
		    if ($ext->settings['enable_debug'] == true) {
		      erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
		    }
		};
	}
}

exit();
?>