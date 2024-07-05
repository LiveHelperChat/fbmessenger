<h1>Login to facebook</h1>

<?php

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email', 'manage_pages', 'pages_show_list', 'pages_messaging', 'pages_messaging_subscriptions']; // Optional permissions

try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) { ?>
    <?php $errors[] = $e->getMessage();$loginUrl = $helper->getLoginUrl('https://'.$_SERVER['HTTP_HOST']. erLhcoreClassDesign::baseurl('fbmessenger/fbcallback'), $permissions); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <a class="btn btn-default" href="<?php echo $loginUrl?>">Try login again!</a>
    <?php return; ?>
<?php } catch(Facebook\Exceptions\FacebookSDKException $e) { ?>
    <?php $errors[] = $e->getMessage();$loginUrl = $helper->getLoginUrl('https://'.$_SERVER['HTTP_HOST']. erLhcoreClassDesign::baseurl('fbmessenger/fbcallback'), $permissions); ?>
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
        <?php $errors[] = 'Error getting long-lived access token: ' . $helper->getMessage(); $helper->getLoginUrl('https://'.$_SERVER['HTTP_HOST']. erLhcoreClassDesign::baseurl('fbmessenger/fbcallback'), $permissions); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <a class="btn btn-default" href="<?php echo $loginUrl?>">Try login again!</a>
        <?php return; ?>
    <?php  }
}

$_SESSION['fb_access_token'] = (string) $accessToken;

$fbUser = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())));

if (!($fbUser instanceof erLhcoreClassModelFBMessengerUser)) {
    $fbUser = new erLhcoreClassModelFBMessengerUser();
    $fbUser->user_id = erLhcoreClassUser::instance()->getUserID();
    $fbUser->fb_user_id =  $tokenMetadata->getUserId();
    $fbUser->access_token = $accessToken;
    $fbUser->saveThis();
} else {
    $fbUser->user_id = erLhcoreClassUser::instance()->getUserID();
    $fbUser->fb_user_id =  $tokenMetadata->getUserId();
    $fbUser->access_token = $accessToken;
    $fbUser->saveThis();
}

header('Location: ' .erLhcoreClassDesign::baseurldirect('fbmessenger/myfbpages'));
exit;

?>