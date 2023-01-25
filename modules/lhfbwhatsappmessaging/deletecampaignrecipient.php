<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

try {

    $item = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch( $Params['user_parameters']['id']);
    if ($item->can_delete) {
        $item->removeThis();
        erLhcoreClassModule::redirect('fbwhatsappmessaging/campaignrecipient','/(campaign)/' . $item->campaign_id);
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