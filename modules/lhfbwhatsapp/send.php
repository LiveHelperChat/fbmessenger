<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/send.tpl.php');

$item = new LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage();
$templates = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->getTemplates();
$phones = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->getPhones();

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbwhatsapp/send');
        exit;
    }

    $definition = array(
        'phone' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'template' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'phone_sender_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'dep_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'field_1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_2' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_3' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_4' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_5' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_6' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_2' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_3' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_4' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_5' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_6' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_doc_1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_doc_filename_1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_img_1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'field_header_video_1' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ($form->hasValidData( 'phone' ) && $form->phone != '') {
        $item->phone = $form->phone;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please enter a phone');
    }

    if ($form->hasValidData( 'dep_id' )) {
        $item->dep_id = $form->dep_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a department!');
    }

    if ($form->hasValidData( 'phone_sender_id' )) {
        $item->phone_sender_id = $form->phone_sender_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a send phone!');
    }

    $messageVariables = $item->message_variables_array;

    for ($i = 0; $i < 6; $i++) {
        if ($form->hasValidData('field_' . $i) && $form->{'field_' . $i}) {
            $messageVariables['field_' . $i] = $form->{'field_' . $i};
        }
    }

    for ($i = 0; $i < 6; $i++) {
        if ($form->hasValidData('field_header_' . $i) && $form->{'field_header_' . $i}) {
            $messageVariables['field_header_' . $i] = $form->{'field_header_' . $i};
        }
    }

    for ($i = 0; $i < 6; $i++) {
        if ($form->hasValidData('field_header_img_' . $i) && $form->{'field_header_img_' . $i}) {
            $messageVariables['field_header_img_' . $i] = $form->{'field_header_img_' . $i};
        }
    }

    for ($i = 0; $i < 6; $i++) {
        if ($form->hasValidData('field_header_video_' . $i) && $form->{'field_header_video_' . $i}) {
            $messageVariables['field_header_video_' . $i] = $form->{'field_header_video_' . $i};
        }
    }

    for ($i = 0; $i < 6; $i++) {
        if ($form->hasValidData('field_header_doc_' . $i) && $form->{'field_header_doc_' . $i}) {
            $messageVariables['field_header_doc_' . $i] = $form->{'field_header_doc_' . $i};
            $messageVariables['field_header_doc_filename_' . $i] = $form->hasValidData('field_header_doc_filename_' . $i) && $form->{'field_header_doc_filename_' . $i} ? $form->{'field_header_doc_filename_' . $i} : '';
        }
    }

    $item->message_variables_array = $messageVariables;
    $item->message_variables = json_encode($messageVariables);

    if ($form->hasValidData( 'template' ) && $form->template != '') {
        $template = explode('||',$form->template);
        $item->template = $template[0];
        $item->language = $template[1];
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a template!');
    }

    if (count($Errors) == 0) {
        try {

            LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->sendTemplate($item, $templates, $phones);

            $item->user_id = $currentUser->getUserID();
            $item->saveThis();

            $tpl->set('updated',true);
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }
    } else {
        $tpl->set('errors',$Errors);
    }

}


$tpl->setArray([
    'send' => $item,
    'templates' => $templates,
    'phones' => $phones
]);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/extension.fbwhatsapp.js').'"></script>';

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Send')
    )
);

?>