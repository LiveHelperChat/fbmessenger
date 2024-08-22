<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/whatsappsubscribe.tpl.php');

$fb = erLhcoreClassModelFBMessengerUser::getFBApp();

// https://developers.facebook.com/docs/graph-api/webhooks/getting-started/webhooks-for-whatsapp#subscribe
// https://developers.facebook.com/docs/whatsapp/business-management-api/guides/set-up-webhooks
//  Your App is not linked to a business or the business doesn't have access to the WhatsApp Business Account. Please go to Meta Business Settings - in Apps tab under Account, click 'Add' and select 'Connect an app ID' to link your App to your business.
//$response = $fb->post('/' . $Params['user_parameters']['whatsapp_business_account_id'] . '/subscribed_apps', array('subscribed_fields' => array('messages')));

try {

    if ($Params['user_parameters_unordered']['action'] == 'unsubscribe') {

        $pageMy = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('whatsapp_business_phone_number_id' => $Params['user_parameters']['phone_number_id'], 'whatsapp_business_account_id' => $Params['user_parameters']['whatsapp_business_account_id'])));

        if ($pageMy instanceof erLhcoreClassModelMyFBPage) {
            $pageMy->removeThis();
        }

        $response = $fb->delete('/' .  $Params['user_parameters']['whatsapp_business_account_id'] . '/subscribed_apps');
        $bodyResponse = $response->getDecodedBody();

        if ($bodyResponse['success'] == 1) {
            //\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatActivator::remove();
            $tpl->set('unsubscribed', true);
        } else {
            $tpl->set('errors', array('We could not un-subscription'));
        }

    } else {
        $response = $fb->post('/' . $Params['user_parameters']['whatsapp_business_account_id'] . '/subscribed_apps', array('subscribed_fields' => array('messages')));
        $bodyResponse = $response->getDecodedBody();

        if ($bodyResponse['success'] == 1) {
            $tpl->set('subscribed', true);

            $pageMy = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('whatsapp_business_phone_number_id' => $Params['user_parameters']['phone_number_id'], 'whatsapp_business_account_id' => $Params['user_parameters']['whatsapp_business_account_id'])));

            if (!($pageMy instanceof erLhcoreClassModelMyFBPage)) {
                $pageMy = new erLhcoreClassModelMyFBPage();
            } else {
                $pageMy = new erLhcoreClassModelMyFBPage();
            }

            $pageMy->dep_id = $Params['user_parameters_unordered']['dep'];
            $pageMy->enabled = 1;
            $pageMy->page_id = 0;
            $pageMy->access_token = $fb->getDefaultAccessToken();
            $pageMy->whatsapp_business_account_id = $Params['user_parameters']['whatsapp_business_account_id'];
            $pageMy->whatsapp_business_phone_number_id = $Params['user_parameters']['phone_number_id'];
            $pageMy->saveThis();

            $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookwhatsappscope')));

            if (!is_object($webhookPresent)) {
                \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatActivator::installOrUpdate(['dep_id' => $pageMy->dep_id]);
            }
        }
    }

} catch (Exception $e) {

    //print_r($e);

    $responseData = $e->getResponseData();
    $tpl->set('errors', array($e->getMessage() .'. '. $responseData['error']['error_user_title'].'. ' .  $responseData['error']['error_user_msg']));
}

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Facebook messenger')),
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/myfbpages'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages')),
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','WhatsApp subscription'))
);

?>