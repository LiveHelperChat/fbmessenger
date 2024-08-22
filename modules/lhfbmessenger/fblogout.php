<?php

try {
    $user = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())));

    if ($user instanceof erLhcoreClassModelFBMessengerUser) {

        $fb = erLhcoreClassModelFBMessengerUser::getFBApp(false);

        if ($fb !== false) {

            try {
                $response = $fb->get('me/accounts?type=page');
            } catch (Exception $e) {
                $user->removeThis();
                header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/fbmessenger/index') );
                exit;
            }

            $bodyResponse = $response->getDecodedBody();

            foreach ($bodyResponse['data'] as $page) {
                $pageMy = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('page_id' => $page['id'])));

                // Remove APP from pages
                if ($pageMy instanceof erLhcoreClassModelMyFBPage) {
                    $pageMy->removeThis();

                    try {
                        $response = $fb->delete('/' . $page['id'] . '/subscribed_apps', array(), $page['access_token']);
                    } catch (Exception $e) {

                    }
                }
            }
        }

        $user->removeThis();
    }

    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/fbmessenger/index') );
    exit;

} catch (Exception $e) {

    header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/fbmessenger/index') );
    exit;

    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors', array($e->getMessage()));
    $Result['content'] = $tpl->fetch();
    $Result['path'] = array(
        array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook messenger')),
        array('url' => erLhcoreClassDesign::baseurl('fbmessenger/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook logout'))
    );
}


?>