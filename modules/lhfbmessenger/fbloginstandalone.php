<?php

$fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();

$helper = $fb->getRedirectLoginHelper();

$permissions = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['scopes']; // Optional permissions

$sessionCookieName = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'php_session_cookie_name', false );

if (!empty($sessionCookieName) && $sessionCookieName !== false) {
    session_name($sessionCookieName);
}

@session_start();

$_SESSION['lhc_instance'] = $Params['user_parameters']['id'];
$_SESSION['lhc_instance_uid'] = $Params['user_parameters']['uid'];

$verifyHash = sha1(erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['secret_hash'] . '_' . erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['address'] . '_' .  $Params['user_parameters']['id'] . '_' . $Params['user_parameters']['uid'] . $Params['user_parameters']['time']);

if ($verifyHash == $Params['user_parameters']['hash'])
{
    header('Location: ' . $helper->getReRequestUrl(erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['address'] . erLhcoreClassDesign::baseurl('fbmessenger/fbcallbackstandalone'), $permissions));
    exit;
} else {
    echo "Invalid hash!";
    exit;
}

?>