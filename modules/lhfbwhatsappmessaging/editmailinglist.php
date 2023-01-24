<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/editmailinglist.tpl.php');

$item = LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (isset($_POST['Cancel_page'])) {
        erLhcoreClassModule::redirect('fbwhatsappmessaging/mailinglist');
        exit ;
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbwhatsappmessaging/mailinglist');
        exit;
    }

    $Errors = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::validateMailingList($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            if (isset($_POST['Update_page'])) {
                $tpl->set('updated',true);
            } else {
                erLhcoreClassModule::redirect('fbwhatsappmessaging/mailinglist');
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
));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index') ,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Facebook chat'),
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('fbwhatsappmessaging/mailinglist'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Mailing list')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Edit')
    )
);

?>