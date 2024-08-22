<?php
$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/newchannel.tpl.php');

$item = new erLhcoreClassModelFBChannel();

$tpl->set('item',$item);

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassFBValidator::validateChannel($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            erLhcoreClassModule::redirect('fbmessenger/channels');
            exit ;

        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$Result['content'] = $tpl->fetch();
$Result['hide_right_column'] = true;
$Result['path'] = array(
    array('url' =>erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook messenger')),
    array (
        'url' =>erLhcoreClassDesign::baseurl('fbmessenger/channels'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Segments')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'New Segment')
    )
);

?>