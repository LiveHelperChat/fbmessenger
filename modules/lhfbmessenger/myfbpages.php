<?php

try {

    $tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/myfbpages.tpl.php');

    $fb = erLhcoreClassModelFBMessengerUser::getFBApp();

    $response = $fb->get('me/accounts?type=page&limit=1000');

    $currentPages = erLhcoreClassModelMyFBPage::getList();

    $pagesRemapped = array();
    $pagesRemappedWhatsApp = array();
    foreach ($currentPages as $currentPage) {
        $pagesRemapped[$currentPage->page_id] = $currentPage;
        $pagesRemappedWhatsApp[$currentPage->whatsapp_business_account_id] = $currentPage;
    }

    $tpl->set('current_pages', $pagesRemapped);
    $tpl->set('current_pages_whatsapp', $pagesRemappedWhatsApp);
    $tpl->set('pages', $response->getDecodedBody());

    try {
        $response = $fb->get('me?fields=businesses');
        $responseData = $response->getDecodedBody();
        $phoneNumbers = [];

        foreach ($responseData['businesses']['data'] as $dataItem) {
            try {
                $response = $fb->get($dataItem['id'].  '/owned_whatsapp_business_accounts');
                $whatsAppBusinessAccounts = $response->getDecodedBody();
                foreach ($whatsAppBusinessAccounts['data'] as $whatsAppBusinessAccount) {
                    $response = $fb->get($whatsAppBusinessAccount['id'] . '/phone_numbers');
                    $phoneNumbersData = $response->getDecodedBody();
                    foreach ($phoneNumbersData['data'] as $phoneNumber) {
                        $phoneNumber['whatsapp_business_account_id'] = $whatsAppBusinessAccount['id'];
                        $phoneNumber['whatsapp_business_account_name'] = $whatsAppBusinessAccount['name'];
                        $phoneNumber['business_id'] = $dataItem['id'];
                        $phoneNumber['business_name'] = $dataItem['name'];
                        $phoneNumbers[] = $phoneNumber;
                    }
                }
            } catch (Exception $e) {

            }
        }
        $tpl->set('phone_numbers', $phoneNumbers);
    } catch (Exception $e){ // Not all busienss we can manage

    }

} catch (Exception $e) {
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors', array($e->getMessage()));
}


$Result['content'] = $tpl->fetch();
$Result['path'] = array(
	array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook messenger')),
	array('url' => erLhcoreClassDesign::baseurl('fbmessenger/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages'))
);

?>