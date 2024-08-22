<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/editchannel.tpl.php');

$item =  erLhcoreClassModelFBChannel::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {

    if (isset($_POST['Cancel_action'])) {
        erLhcoreClassModule::redirect('fbmessenger/channels');
        exit ;
    }

    $Errors = erLhcoreClassFBValidator::validateChannel($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            erLhcoreClassModule::redirect('fbmessenger/channels');
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
$Result['hide_right_column'] = true;

$Result['path'] = array(
    array('url' =>erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook messenger')),
    array (
        'url' =>erLhcoreClassDesign::baseurl('fbmessenger/channels'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Segments')
    ),
    array (
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Edit segment')
    )
);

?>