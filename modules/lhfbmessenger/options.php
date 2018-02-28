<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/options.tpl.php');

$fbOptions = erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
$data = (array)$fbOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    $definition = array(
        'new_chat' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'exclude_workflow' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'priority' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'new_chat' ) && $form->new_chat == true ) {
        $data['new_chat'] = 1;
    } else {
        $data['new_chat'] = 0;
    }

    if ( $form->hasValidData( 'exclude_workflow' ) && $form->exclude_workflow == true ) {
        $data['exclude_workflow'] = 1;
    } else {
        $data['exclude_workflow'] = 0;
    }

    if ( $form->hasValidData( 'priority' )) {
        $data['priority'] = $form->priority;
    } else {
        $data['priority'] = 0;
    }

    $fbOptions->explain = '';
    $fbOptions->type = 0;
    $fbOptions->hidden = 1;
    $fbOptions->identifier = 'fbmessenger_options';
    $fbOptions->value = serialize($data);
    $fbOptions->saveThis();
    
    $tpl->set('updated','done');
}

$tpl->set('fb_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'Facebook Chat')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'Options')
    )
);

?>