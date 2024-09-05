<?php

// Works as proxy to child independent instances

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

$data = json_decode(file_get_contents('php://input'), true);

try {
    if (isset($data['entry'][0]['id']) && is_numeric($data['entry'][0]['id'])) {
        $db = ezcDbInstance::get();

        $stmt = $db->prepare("SELECT address, instance_id FROM lhc_fbmessenger_standalone_fb_page WHERE instagram_business_account = :instagram_business_account");
        $stmt->bindValue(':instagram_business_account', $data['entry'][0]['id']);
        $stmt->execute();
        $addressInstance = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($addressInstance['address']) || !empty($addressInstance['instance_id'])) {

            if (!empty($addressInstance['address'])) {
                $addressInstance = $addressInstance['address'];
            } else {
                $cfg = erConfigClassLhConfig::getInstance();
                $instance = erLhcoreClassModelInstance::fetch($addressInstance['instance_id']);
                $addressInstance = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'http_mode').$instance->address . '.' . $cfg->getSetting( 'site', 'seller_domain');
            }

            erLhcoreClassFBValidator::proxyStandaloneRequest([
                'headers' => [
                    'Facebook-API-Version: '.$_SERVER['HTTP_FACEBOOK_API_VERSION'],
                    'X-Hub-Signature: '.$_SERVER['HTTP_X_HUB_SIGNATURE']
                ],
                'body' => file_get_contents('php://input'),
                'address' => 'https://'.$addressInstance.'/fbmessenger/callbackinstagram'
            ]);

        } else {
            throw new Exception('Page not found to proxy - ' . json_encode($data));
        }
    }
} catch (\Exception $e){
    if ($ext->settings['enable_debug'] == true) {
        erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
    }
}

exit();

?>