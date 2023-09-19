<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/editaccount.tpl.php');

$item = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbwhatsapp/account');
        exit;
    }

    if (isset($_POST['UpdatePhones_account'])) {

        try {
            $instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();
            $instance->setAccessToken($item->access_token);
            $instance->setBusinessAccountID($item->business_account_id);

            $phones = $instance->getPhones();
            $phonesIds = [];

            $tpl->set('phonesUpdated', $phones);

            foreach ($phones as $phone) {
                $phonesIds[] = $phone['id'];
            }

            $item->phone_number_ids = json_encode($phonesIds);
            $item->updateThis();
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $Errors = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccountValidator::validateAccount($item);

        if (count($Errors) == 0) {
            try {
                $item->saveThis();
                erLhcoreClassModule::redirect('fbwhatsapp/account');
                exit ;

            } catch (Exception $e) {
                $tpl->set('errors',array($e->getMessage()));
            }
        } else {
            $tpl->set('errors',$Errors);
        }
    }
}

$tpl->set('item',$item);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')
    ),
    array (
        'url' =>erLhcoreClassDesign::baseurl('fbwhatsapp/account'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Business Accounts')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Edit Business Account')
    )
);

?>