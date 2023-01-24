<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/massmessage.tpl.php');

$itemDefault = new LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage();
$instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

if (isset($_POST['business_account_id']) && $_POST['business_account_id'] > 0) {
    $Params['user_parameters_unordered']['business_account_id'] = (int)$_POST['business_account_id'];
}

if (is_numeric($Params['user_parameters_unordered']['business_account_id'])) {
    $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($Params['user_parameters_unordered']['business_account_id']);
    $instance->setAccessToken($account->access_token);
    $instance->setBusinessAccountID($account->business_account_id);
    $tpl->set('business_account_id', $account->id);
}

$templates = $instance->getTemplates();
$phones = $instance->getPhones();

if (isset($_POST['UploadFileAction'])) {

    $errors = [];

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbwhatsapp/messages');
        exit;
    }

    $definition = array(
        'dep_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'template' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'phone_sender_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();


    if ($form->hasValidData( 'dep_id' )) {
        $itemDefault->dep_id = $form->dep_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a department!');
    }

    if ($form->hasValidData( 'phone_sender_id' )) {
        $itemDefault->phone_sender_id = $form->phone_sender_id;
        foreach ($phones as $phone) {
            if ($itemDefault->phone_sender_id == $phone['id']) {
                $itemDefault->phone_sender = $phone['display_phone_number'];
            }
        }
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a sender phone!');
    }

    if ($form->hasValidData( 'template' ) && $form->template != '') {
        $template = explode('||',$form->template);
        $itemDefault->template = $template[0];
        $itemDefault->language = $template[1];
        $itemDefault->template_id = $template[2];
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Please choose a template!');
    }

    if (empty($Errors) && erLhcoreClassSearchHandler::isFile('files',array('csv'))) {

        $dir = 'var/tmpfiles/';
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath', array('dir' => & $dir));

        erLhcoreClassFileUpload::mkdirRecursive( $dir );

        $filename = erLhcoreClassSearchHandler::moveUploadedFile('files', $dir);

        $header = NULL;
        $data = array();

        if (($handle = fopen($dir . $filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 10000, ",")) !== FALSE)
            {
                if(!$header) {
                    $header = $row;
                } else {
                    if (count($header) != count($row)) {
                        $row = $row + array_fill(count($row),count($header) - count($row),'');
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        unlink($dir . $filename);

        $canned = ['phone','phone_whatsapp',
            'field_1', 'field_2', 'field_3', 'field_4', 'field_5', 'field_6',
            'field_header_1', 'field_header_2', 'field_header_3', 'field_header_4', 'field_header_5', 'field_header_6',
            'field_header_doc_1', 'field_header_doc_filename_1',
            'field_header_img_1', 'field_header_video_1',
        ];

        $stats = array(
            'imported' => 0,
        );

        if ($canned === $header) {

            foreach ($data as $item) {
                $messagePrepared = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage();
                $messagePrepared->user_id = $currentUser->getUserID();
                $messagePrepared->phone = str_replace('+','',$item['phone']);
                $messagePrepared->phone_whatsapp = str_replace('+','',$item['phone_whatsapp']);
                $messagePrepared->phone_sender = $itemDefault->phone_sender;
                $messagePrepared->phone_sender_id = $itemDefault->phone_sender_id;
                $messagePrepared->business_account_id = is_object($account) ? $account->id : 0;
                unset($item['phone']);
                $messagePrepared->message_variables = json_encode($item);
                $messagePrepared->template = $itemDefault->template;
                $messagePrepared->language = $itemDefault->language;
                $messagePrepared->template_id = $itemDefault->template_id;
                $messagePrepared->dep_id = $itemDefault->dep_id;
                $messagePrepared->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING_PROCESS;
                $messagePrepared->saveThis();
                $stats['imported']++;
            }

            $tpl->set('update', $stats);

        } else {
            $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Expected columns does not match!')]);
        }

    } elseif (!empty($Errors)) {
        $tpl->set('errors', $Errors);
    } else {
        $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Invalid file format')]);
    }
}

$tpl->setArray([
    'send' => $itemDefault,
    'templates' => $templates,
    'phones' => $phones
]);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/extension.fbwhatsapp.js').'"></script>';
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Mass message')
    )
);

?>