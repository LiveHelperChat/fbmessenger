<?php

$fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();
$helper = $fb->getRedirectLoginHelper();
$permissions = ['instagram_manage_messages', 'instagram_basic', 'pages_manage_metadata', 'pages_read_engagement']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '.' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain') . erLhcoreClassDesign::baseurl('fbmessenger/fbcallbackinstanceinstagram'), $permissions);
try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) { ?>
    <?php $errors[] = $e->getMessage() ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <a class="btn btn-default" href="<?php echo $loginUrl?>">Try login again!</a>
    <?php return; ?>
<?php } catch(Facebook\Exceptions\FacebookSDKException $e) { ?>
    <?php $errors[] = $e->getMessage() ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <a class="btn btn-default" href="<?php echo $loginUrl?>">Try login again!</a>
    <?php return; ?>
<?php }
if (! isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}
// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();
// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
//echo '<h3>Metadata</h3>';
//var_dump($tokenMetadata);
// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId(erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['app_settings']['app_id']); // app_id app_idReplace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();
if (! $accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) { ?>
        <?php $errors[] = 'Error getting long-lived access token: ' . $helper->getMessage() ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <a class="btn btn-default" href="<?php echo $loginUrl?>">Try login again!</a>
        <?php return; ?>
    <?php  }
}
$_SESSION['fb_access_token'] = (string) $accessToken;
if (is_numeric($_SESSION['lhc_instance']) && is_numeric($_SESSION['lhc_instance_uid'])) {
    $cfg = erConfigClassLhConfig::getInstance();
    $instance = erLhcoreClassModelInstance::fetch($_SESSION['lhc_instance']);
    // Switch to customer DB
    $db = ezcDbInstance::get();
    $db->query('USE '.$cfg->getSetting( 'db', 'database_user_prefix').$instance->id);
    $fbUser = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => $_SESSION['lhc_instance_uid'])));
    if (!($fbUser instanceof erLhcoreClassModelFBMessengerUser)) {
        /*$fbUser = new erLhcoreClassModelFBMessengerUser();
        $fbUser->user_id = $_SESSION['lhc_instance_uid'];
        $fbUser->fb_user_id =  $tokenMetadata->getUserId();
        $fbUser->access_token = $accessToken;
        $fbUser->saveThis();*/
    } else {
        /*$fbUser->user_id = $_SESSION['lhc_instance_uid'];
        $fbUser->fb_user_id =  $tokenMetadata->getUserId();
        $fbUser->access_token = $accessToken;
        $fbUser->saveThis();*/
    }
    echo "herer";
    /*header('Location: https://' . $instance->address . '.' .  erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain') . '/site_admin/' .erLhcoreClassDesign::baseurldirect('fbmessenger/myfbpages'));
    exit;*/
}
exit;

?>