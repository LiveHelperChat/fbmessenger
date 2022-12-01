<?php
$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/newaccount.tpl.php');

$item = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount();

$tpl->set('item',$item);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbmessenger/bbcode');
        exit;
    }

    $Errors = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccountValidator::validateAccount($item);

    if (count($Errors) == 0) {
        try {
            $instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();
            $instance->setAccessToken($item->access_token);
            $instance->setBusinessAccountID($item->business_account_id);

            $phones = $instance->getPhones();
            $phonesIds = [];

            foreach ($phones as $phone) {
                $phonesIds[] = $phone['id'];
            }

            $item->phone_number_ids = json_encode($phonesIds);
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
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'New Business Account')
    )
);

?>