<?php

$permissions = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['scopes'];

$sessionCookieName = erConfigClassLhConfig::getInstance()->getSetting( 'site', 'php_session_cookie_name', false );

if (!empty($sessionCookieName) && $sessionCookieName !== false) {
    session_name($sessionCookieName);
};

@session_start();

$_SESSION['lhc_instance'] = $Params['user_parameters']['id'];
$_SESSION['lhc_instance_uid'] = $Params['user_parameters']['uid'];

$verifyHash = sha1(erConfigClassLhConfig::getInstance()->getSetting('site','seller_secret_hash') . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '_' .  $Params['user_parameters']['id'] . '_' . $Params['user_parameters']['uid'] . $Params['user_parameters']['time']);
if ($verifyHash == $Params['user_parameters']['hash'])
{
    $fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();
    $helper = $fb->getRedirectLoginHelper();
    header('Location: ' . $helper->getReRequestUrl('https://'.  erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '.' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain') . erLhcoreClassDesign::baseurl('fbmessenger/fbcallbackinstance') . '?enforce_https=1', $permissions));
    exit;
} else {
    echo "Invalid hash!";
    exit;
}
?>