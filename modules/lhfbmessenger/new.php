<?php
$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/new.tpl.php');

$item = new erLhcoreClassModelFBPage();

$tpl->set('item',$item);

if (ezcInputForm::hasPostData()) {

    $Errors = erLhcoreClassFBValidator::validatePage($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
             
            erLhcoreClassModule::redirect('fbmessenger/list');
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
    array (
        'url' =>erLhcoreClassDesign::baseurl('fbmessenger/list'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'New facebook page')
    )
);

?>