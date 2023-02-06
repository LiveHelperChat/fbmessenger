<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/newmailinglist.tpl.php');

$item = new LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList();

if (isset($_POST['Cancel_page'])) {
    erLhcoreClassModule::redirect('fbwhatsappmessaging/mailinglist');
    exit;
}

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbwhatsappmessaging/mailinglist');
        exit;
    }

    $items = array();

    $Errors = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::validateMailingList($item);

    if (count($Errors) == 0) {
        try {
            $item->user_id = $currentUser->getUserID();
            $item->saveThis();
            erLhcoreClassModule::redirect('fbwhatsappmessaging/mailinglist');
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

$Result['path'] = array(
    array(
            'url' => erLhcoreClassDesign::baseurl('fbmessenger/index') ,
            'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat'),
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('fbwhatsappmessaging/mailinglist'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Recipients lists')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'New')
    )
);

?>