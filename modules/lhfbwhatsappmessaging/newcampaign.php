<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/newcampaign.tpl.php');

$item = new LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign();

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailing/campaign');
        exit;
    }

    $items = array();

    $Errors = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::validateCampaign($item);

    if (count($Errors) == 0) {
        try {
            $item->user_id = $currentUser->getUserID();
            $item->saveThis();

            if (isset($_POST['Save_continue'])) {
                erLhcoreClassModule::redirect('fbwhatsappmessaging/campaignrecipient','/(campaign)/' . $item->id);
            } else {
                erLhcoreClassModule::redirect('fbwhatsappmessaging/campaign');
            }

            exit;
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }
    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::design('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index') ,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Facebook chat'),
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaign'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Campaigns')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'New')
    )
);

?>