<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

try {
    $item = LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch( $Params['user_parameters']['id']);
    if ($item->can_delete) {
        $item->removeThis();
        erLhcoreClassModule::redirect('fbwhatsappmessaging/mailingrecipient', (is_array($Params['user_parameters_unordered']['ml']) && !empty($Params['user_parameters_unordered']['ml']) ? '/(ml)/' . implode('/', $Params['user_parameters_unordered']['ml']) : ''));
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