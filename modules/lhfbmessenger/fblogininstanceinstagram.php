<?php
$fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'pages_show_list', 'pages_messaging', 'instagram_manage_messages', 'instagram_basic', 'pages_manage_metadata', 'pages_read_engagement'];
@session_start();
$_SESSION['lhc_instance'] = $Params['user_parameters']['id'];
$_SESSION['lhc_instance_uid'] = $Params['user_parameters']['uid'];
$verifyHash = sha1(erConfigClassLhConfig::getInstance()->getSetting('site','seller_secret_hash') . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '_' .  $Params['user_parameters']['id'] . '_' . $Params['user_parameters']['uid'] . $Params['user_parameters']['time']);
if ($verifyHash == $Params['user_parameters']['hash'])
{
    header('Location: ' . $helper->getReRequestUrl('https://'.  erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '.' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain') . erLhcoreClassDesign::baseurl('fbmessenger/fbcallbackinstance') . '?enforce_https=1', $permissions));
    exit;
} else {
    echo "Invalid hash!";
    exit;
}
?>