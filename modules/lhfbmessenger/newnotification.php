<?php
$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/newnotification.tpl.php');

$item = new erLhcoreClassModelFBNotificationSchedule();

$tpl->set('item',$item);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbmessenger/notifications');
        exit;
    }

    $Errors = erLhcoreClassFBValidator::validateNotification($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            erLhcoreClassModule::redirect('fbmessenger/notifications');
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
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')),
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/notifications'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook notifications')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'New facebook notification'))
);

?>