<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/limitgenerator.tpl.php');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')
    ),
    array (
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Limit generator')
    )
);

?>