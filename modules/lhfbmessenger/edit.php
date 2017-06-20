<?php 

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/edit.tpl.php');

$item =  erLhcoreClassModelFBPage::fetch($Params['user_parameters']['id']);

if (ezcInputForm::hasPostData()) {
        
    if (isset($_POST['Cancel_action'])) {
        erLhcoreClassModule::redirect('fbmessenger/list');
        exit ;
    }
    
    $Errors = erLhcoreClassFBValidator::validatePage($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();
                       
            erLhcoreClassModule::redirect('fbmessenger/list');
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
        'url' =>erLhcoreClassDesign::baseurl('fbmessenger/list'), 
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages')        
    ),
    array (       
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Edit page')
    )
);

?>