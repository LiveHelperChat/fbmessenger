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

$webookHandler = new WebhookRequestHandler($ext->getPage()->app_secret, $ext->getPage()->verify_token);

if (!$webookHandler->isValidCallbackRequest()) {    
    if ($ext->settings['enable_debug'] == true) {
	   erLhcoreClassLog::write('INVALID__TOKEN');
    }
	exit;
}

$events = $webookHandler->getAllCallbackEvents();

foreach($events as $event) {
		
	if ($event instanceof MessageEvent) {
		
		if ($ext->settings['enable_debug'] == true) {
    		erLhcoreClassLog::write('Message - '.$event->getMessageText());
    		erLhcoreClassLog::write('Sender User Id - '.$event->getSenderId());
		}
		
	    try {
		      $ext->processVisitorMessage($event);
		} catch (Exception $e) {
		    if ($ext->settings['enable_debug'] == true) {
		      erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
		    }
		};
	}
}

exit();
?>