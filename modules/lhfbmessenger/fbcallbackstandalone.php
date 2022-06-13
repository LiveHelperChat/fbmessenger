<?php

session_name('LHC_SESSID');

$fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email', 'pages_show_list', 'pages_messaging', 'pages_messaging_subscriptions']; // Optional permissions

$loginUrl = $helper->getLoginUrl(erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['address'] . erLhcoreClassDesign::baseurl('fbmessenger/fbcallbackinstance'), $permissions);


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
        /*header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";*/
        header('Location: https://' . $_SESSION['lhc_instance'] . erLhcoreClassDesign::baseurl('fbmessenger/index'));
        exit;
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

if (is_string($_SESSION['lhc_instance']) && !empty($_SESSION['lhc_instance']) && is_numeric($_SESSION['lhc_instance_uid'])) {

    try {
        erLhcoreClassFBValidator::registerStandalonePage([
            'access_token' => (string) $accessToken,
            'fb_user_id' => $tokenMetadata->getUserId(),
            'user_id' => $_SESSION['lhc_instance_uid'],
            'address' => $_SESSION['lhc_instance'],
        ]);

        // Redirect back to subdomain
        header('Location: https://' . $_SESSION['lhc_instance'] . erLhcoreClassDesign::baseurl('fbmessenger/myfbpages'));
        exit;

    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }

}

exit;