<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/editmailingrecipient.tpl.php');

$item = LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        die('Invalid CSRF Token');
        exit;
    }

    $Errors = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::validateMailingRecipient($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            $tpl->set('updated',true);
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->setArray(array(
    'item' => $item,
));

echo $tpl->fetch();
exit;

?>