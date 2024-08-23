<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/pagesubscribe.tpl.php');

$fb = erLhcoreClassModelFBMessengerUser::getFBApp();

$response = $fb->get('me/accounts?type=page&limit=1000');

$bodyResponse = $response->getDecodedBody();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

foreach ($bodyResponse['data'] as $page) {
    if ($Params['user_parameters']['id'] == $page['id']) {

        try {
            if ($Params['user_parameters_unordered']['action'] == 'unsubscribe') {

                $pageMy = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('page_id' => $page['id'])));

                if ($pageMy instanceof erLhcoreClassModelMyFBPage) {
                    $pageMy->removeThis();
                }

                //$response = $fb->delete('/' . $page['id'] . '/subscribed_apps', array(), $page['access_token']);

                $extFb = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');
                $response = $fb->delete('/' . $page['id'] . '/subscribed_apps', array(), $extFb->settings['app_settings']['app_id'].'|'.$extFb->settings['app_settings']['app_secret']);
                $bodyResponse = $response->getDecodedBody();

                if ($bodyResponse['success'] == 1) {
                    $tpl->set('unsubscribed', true);
                } else {
                    $tpl->set('errors', array('We could not un-subscription'));
                }

            } else {

                if (!is_numeric($Params['user_parameters_unordered']['dep'])) {
                    $tpl->set('errors', array('Department not chosen!'));
                } else {

                    $response = $fb->post('/' . $page['id'] . '/subscribed_apps', array('subscribed_fields' => array('messages', 'messaging_postbacks', 'message_deliveries', 'message_reads', 'messaging_pre_checkouts', 'messaging_checkout_updates', 'messaging_referrals', 'message_echoes', 'standby', 'messaging_handovers', 'message_reactions')), $page['access_token']);

                    $bodyResponse = $response->getDecodedBody();

                    if ($bodyResponse['success'] == 1) {
                        $pageMy = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('page_id' => $page['id'])));

                        if (!($pageMy instanceof erLhcoreClassModelMyFBPage)) {
                            $pageMy = new erLhcoreClassModelMyFBPage();
                        } else {
                            $pageMy = new erLhcoreClassModelMyFBPage();
                        }

                        $pageMy->dep_id = $Params['user_parameters_unordered']['dep'];
                        $pageMy->access_token = $page['access_token'];
                        $pageMy->enabled = 1;
                        $pageMy->page_id = $page['id'];

                        try {
                            $responseINST = $fb->get('/' . $page['id'] . '/?fields=instagram_business_account', $page['access_token']);
                            $instagramAccountData = $responseINST->getDecodedBody();

                            if (isset($instagramAccountData['instagram_business_account']['id'])) {
                                $pageMy->instagram_business_account = $instagramAccountData['instagram_business_account']['id'];
                            }

                        } catch (Exception $e) {

                        }

                        $pageMy->saveThis();

                        // Set default page settings
                        $ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');
                        $ext->setPage($pageMy);
                        $settings = erLhcoreClassModelChatConfig::fetch('fb_page_' . $pageMy->id . '_settings');

                        $dataArray = array();
                        $dataArray['greeting_text'] = 'Hello, how can we help you?';
                        $dataArray['get_started_button_payload'] = 'GET_STARTED';

                        $settings->explain = '';
                        $settings->type = 0;
                        $settings->hidden = 1;
                        $settings->identifier = 'fb_page_' . $pageMy->id . '_settings';
                        $settings->value = json_encode($dataArray);
                        $settings->saveThis();

                        erLhcoreClassModule::redirect('fbmessenger/myfbpages');

                        $tpl->set('subscribed', true);
                    } else {
                        $tpl->set('subscribed', false);
                    }
                }
            }
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }
    }
}

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Facebook messenger')),
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/myfbpages'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages')),
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page subscription'))
);

?>