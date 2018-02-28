<?php

try {
    $tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/myfbpages.tpl.php');

    $fb = erLhcoreClassModelFBMessengerUser::getFBApp();

    $response = $fb->get('me/accounts?type=page');

    $currentPages = erLhcoreClassModelMyFBPage::getList();

    $pagesRemapped = array();
    foreach ($currentPages as $currentPage) {
        $pagesRemapped[$currentPage->page_id] = $currentPage;
    }

    $tpl->set('current_pages', $pagesRemapped);
    $tpl->set('pages', $response->getDecodedBody());

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