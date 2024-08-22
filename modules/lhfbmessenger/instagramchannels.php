<?php

try {

    $tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/instagramchannels.tpl.php');

    $fb = erLhcoreClassModelFBMessengerUser::getFBApp();

    $response = $fb->get('me/accounts?type=page');

    $bodyResponse = $response->getDecodedBody();

    foreach ($bodyResponse['data'] as $page) {
        if ('756907487748795' == $page['id']) {
            $responseINST = $fb->get('/756907487748795/?fields=instagram_business_account', $page['access_token']);
            $instagramAccountData = $responseINST->getDecodedBody();

            var_dump($instagramAccountData['instagram_business_account']['id']);

            // Instagram account
            // 1527712067323193
            exit;
        }
    }

    // <a class="btn btn-sm btn-danger btn-block" href="/site_admin/fbmessenger/pagesubscribe/756907487748795/(action)/unsubscribe">Un Subscribe</a>


    /*$currentPages = erLhcoreClassModelMyFBPage::getList();

    $pagesRemapped = array();
    foreach ($currentPages as $currentPage) {
        $pagesRemapped[$currentPage->page_id] = $currentPage;
    }

    $tpl->set('current_pages', $pagesRemapped);
    $tpl->set('pages', $response->getDecodedBody());*/
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