<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/options.tpl.php');

$fbOptions = erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
$data = (array)$fbOptions->data;

if ( isset($_POST['StoreOptions']) || isset($_POST['StoreOptionsWhatsApp']) || isset($_POST['StoreOptionsWhatsAppRemove'])  ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbmessenger/options');
        exit;
    }

    $definition = array(
        'whatsapp_access_token' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'whatsapp_verify_token' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'whatsapp_business_account_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'whatsapp_business_account_phone_number' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'whatsapp_access_token' )) {
        $data['whatsapp_access_token'] = $form->whatsapp_access_token;
    } else {
        $data['whatsapp_access_token'] = '';
    }
    
    if ( $form->hasValidData( 'whatsapp_business_account_phone_number' )) {
        $data['whatsapp_business_account_phone_number'] = $form->whatsapp_business_account_phone_number;
    } else {
        $data['whatsapp_business_account_phone_number'] = '';
    }

    if ( $form->hasValidData( 'whatsapp_business_account_id' )) {
        $data['whatsapp_business_account_id'] = $form->whatsapp_business_account_id;
    } else {
        $data['whatsapp_business_account_id'] = '';
    }

    if ( $form->hasValidData( 'whatsapp_verify_token' )) {
        $data['whatsapp_verify_token'] = $form->whatsapp_verify_token;
    } else {
        $data['whatsapp_verify_token'] = '';
    }

    $fbOptions->explain = '';
    $fbOptions->type = 0;
    $fbOptions->hidden = 1;
    $fbOptions->identifier = 'fbmessenger_options';
    $fbOptions->value = serialize($data);
    $fbOptions->saveThis();

    // Update access key instantly
    $incomingWebhook = \erLhcoreClassModelChatIncomingWebhook::findOne(['filter' => ['name' => 'FacebookWhatsApp']]);

    if (is_object($incomingWebhook)) {
        $conditionsArray = $incomingWebhook->conditions_array;
        if (isset($conditionsArray['attr']) && is_array($conditionsArray['attr'])) {
            foreach ($conditionsArray['attr'] as $attrIndex => $attrValue) {
                if ($attrValue['key'] == 'access_token') {
                    $attrValue['value'] = $data['whatsapp_access_token'];
                    $conditionsArray['attr'][$attrIndex] = $attrValue;
                }
            }
        }
        $incomingWebhook->conditions_array = $conditionsArray;
        $incomingWebhook->configuration = json_encode($conditionsArray);
        $incomingWebhook->updateThis(['update' => ['configuration']]);
    }


    if (isset($_POST['StoreOptionsWhatsApp']) ) {
        LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatActivator::installOrUpdate();
    }

    if (isset($_POST['StoreOptionsWhatsAppRemove']) ) {
        LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatActivator::remove();
    }

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