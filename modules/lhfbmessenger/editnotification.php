<?php
$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/editnotification.tpl.php');

$item = erLhcoreClassModelFBNotificationSchedule::fetch($Params['user_parameters']['id']);

$tpl->set('item',$item);

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassFBValidator::validateNotification($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            if (isset($_POST['Update_page'])) {
                $tpl->set('updated',true);
            } else {
                erLhcoreClassModule::redirect('fbmessenger/notifications');
                exit ;
            }

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
    array (
        'url' =>erLhcoreClassDesign::baseurl('fbmessenger/notifications'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook notifications')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Edit facebook notification')
    )
);

?>