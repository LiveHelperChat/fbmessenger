<?php 

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

// Load for which page callback is processed
$fbpage = erLhcoreClassModelFBPage::fetch($Params['user_parameters']['id']);

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $fbpage->verify_token) {
	if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {
	    
	    if ($ext->settings['enable_debug'] == true) {
	        erLhcoreClassLog::write('VERIFIED');
	    }
	    
		echo $_GET['hub_challenge'];
		exit;
	}
}

use Tgallice\FBMessenger\WebhookRequestHandler;

$webookHandler = new WebhookRequestHandler($fbpage->app_secret,$fbpage->verify_token);

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
if (session_id()) session_write_close();

if (function_exists('fastcgi_finish_request')){
    fastcgi_finish_request();
}

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookmessengerappscope')));

if (!is_object($webhookPresent)) {
    \LiveHelperChatExtension\fbmessenger\providers\FBMessengerMessengerAppLiveHelperChatActivator::installOrUpdate(['dep_id' => 0]);
    $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookmessengerappscope')));
}

$identifier = $webhookPresent->identifier;

$Params['user_parameters']['identifier'] = $identifier;
include 'modules/lhwebhooks/incoming.php';

exit();
?>