<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/templates.tpl.php');

try {
    $instance =  LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();
    $templates = $instance->getTemplates();
} catch (Exception $e) {
    $tpl->set('error', $e->getMessage());
    $templates = [];
}

$tpl->set('templates', $templates);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Templates')
    )
);



?>