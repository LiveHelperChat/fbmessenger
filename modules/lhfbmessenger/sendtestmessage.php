<?php

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF Token');
    }

    $lead = erLhcoreClassModelFBLead::fetch($_POST['user_id']);

    if (!($lead instanceof erLhcoreClassModelFBLead)) {
        throw new Exception('Lead could not be found!');
    }

    if (is_object($lead->page)) {
        $messenger = Tgallice\FBMessenger\Messenger::create($lead->page->page_token);

        $messages = erLhcoreClassExtensionFbmessenger::parseMessageForFB(str_replace(array('{first_name}','{last_name}'), array($lead->first_name,$lead->last_name), $_POST['msg']));

        foreach ($messages as $msg) {
            if ($msg !== null) {
                $response = $messenger->sendMessage($lead->user_id, $msg);
            }
        }

        echo json_encode(array('error' => false,'msg' => 'Message was send!'));

    } else {
        throw new Exception('Page could not be found!');
    }

} catch (Exception $e) {
    echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
}

exit;
?>