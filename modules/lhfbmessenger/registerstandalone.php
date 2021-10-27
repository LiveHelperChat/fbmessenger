<?php

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $bodyRequest = file_get_contents('php://input');

    $hash = sha1($bodyRequest.'_'.erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['secret_hash']);

    if ($hash == $Params['user_parameters']['hash']) {
        $requestBody = json_decode($bodyRequest,true);

        $fbUser = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => $requestBody['user_id'])));

        if (!($fbUser instanceof erLhcoreClassModelFBMessengerUser)) {
            $fbUser = new erLhcoreClassModelFBMessengerUser();
            $fbUser->user_id = $requestBody['user_id'];
            $fbUser->fb_user_id = $requestBody['fb_user_id'];
            $fbUser->access_token = $requestBody['access_token'];
            $fbUser->saveThis();
        } else {
            $fbUser->user_id = $requestBody['user_id'];
            $fbUser->fb_user_id = $requestBody['fb_user_id'];
            $fbUser->access_token = $requestBody['access_token'];
            $fbUser->saveThis();
        }

        echo json_encode(array('success' => true));

    } else {
        throw new Exception('Invalid hash verification!');
    }

} catch (Exception $e) {

    http_response_code(400);

    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}



exit;
?>