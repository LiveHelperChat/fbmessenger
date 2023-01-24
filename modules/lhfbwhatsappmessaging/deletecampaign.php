<?php

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

try {
    $item = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($Params['user_parameters']['id']);
    if ($item->can_delete) {
        $item->removeThis();
        erLhcoreClassModule::redirect('fbwhatsappmessaging/campaign');
        exit;
    } else {
        throw new Exception('No permission to edit!');
    }
} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors',array($e->getMessage()));
    $Result['content'] = $tpl->fetch();
}

?>