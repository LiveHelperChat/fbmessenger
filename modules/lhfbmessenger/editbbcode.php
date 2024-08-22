<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/editbbcode.tpl.php');

$item =  erLhcoreClassModelFBBBCode::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbmessenger/bbcode');
        exit;
    }

    if (isset($_POST['Cancel_action'])) {
        erLhcoreClassModule::redirect('fbmessenger/bbcode');
        exit ;
    }

    $Errors = erLhcoreClassFBValidator::validateBBCode($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            erLhcoreClassModule::redirect('fbmessenger/bbcode');
            exit;

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
    array (
        'url' =>erLhcoreClassDesign::baseurl('fbmessenger/bbcode'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook BBCodes')
    ),
    array (
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Edit BBCode')
    )
);

?>