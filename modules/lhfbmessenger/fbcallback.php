<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbmessenger/fbcallback.tpl.php');

$fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();

$tpl->set('fb',$fb);

try {
	$Result['content'] = $tpl->fetch();
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

$Result['path'] = array(
		array('url' =>erLhcoreClassDesign::baseurl('fbmessenger/list'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages'))
);

?>