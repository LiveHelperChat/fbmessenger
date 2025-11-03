<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/whatsappregistercloudapi.tpl.php');

$tpl->set('phoneNumber',[
    'business_id' => (int)$Params['user_parameters']['business_id'],
    'whatsapp_business_account_id' => (int)$Params['user_parameters']['whatsapp_business_account_id'],
    'id' => (int)$Params['user_parameters']['phone_number_id'],
]);

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('fbmessenger/myfbpages');
        exit;
    }

    try {
        $fb = erLhcoreClassModelFBMessengerUser::getFBApp();

        $response = $fb->post('/' . (int)$Params['user_parameters']['phone_number_id'] . '/register', array(
            'messaging_product' => 'whatsapp',
            'pin' => $_POST['pin'],
        ));

        $bodyResponse = $response->getDecodedBody();

        if ($bodyResponse['success'] == 1) {
            $tpl->set('updated',true);
        }

    } catch (Exception $e) {
        $responseData = $e->getResponseData();
        $tpl->set('errors', array($e->getMessage() .'. '. $responseData['error']['error_user_title'].'. ' .  $responseData['error']['error_user_msg']));
    }
}

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'Facebook Chat')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhelasticsearch/module', 'Register In Cloud-API')
    )
);

?>