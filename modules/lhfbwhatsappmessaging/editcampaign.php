<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/editcampaign.tpl.php');

$item = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($Params['user_parameters']['id']);
$tpl->set('tab','');

$instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

if (isset($_POST['business_account_id']) && $_POST['business_account_id'] > 0) {
    $Params['user_parameters_unordered']['business_account_id'] = (int)$_POST['business_account_id'];
}

if ($item->business_account_id > 0) {
    $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($item->business_account_id);
    $instance->setAccessToken($account->access_token);
    $instance->setBusinessAccountID($account->business_account_id);
    $tpl->set('business_account_id', $account->id);
}

$templates = $instance->getTemplates();
$phones = $instance->getPhones();

if (ezcInputForm::hasPostData()) {

    if (isset($_POST['Cancel_page'])) {
        erLhcoreClassModule::redirect('fbwhatsappmessaging/campaign');
        exit ;
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbwhatsappmessaging/campaign');
        exit;
    }

    if (isset($_POST['PauseCampaign'])) {
        \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::pauseCampaign($item);
        erLhcoreClassModule::redirect('fbwhatsappmessaging/editcampaign','/' . $item->id);
        exit;
    }

    $Errors = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::validateCampaign($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            if (isset($_POST['Update_page'])) {
                $tpl->set('updated',true);
            } else {
                erLhcoreClassModule::redirect('fbwhatsappmessaging/campaign');
                exit;
            }

        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->setArray(array(
    'item' => $item,
    'templates' => $templates,
    'phones' => $phones
));

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/extension.fbwhatsapp.js').'"></script>';

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
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Edit')
    )
);

?>