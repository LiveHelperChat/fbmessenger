<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat');?></h1>

<?php
$user = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())));

if (!($user instanceof erLhcoreClassModelFBMessengerUser)) {

    $fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email', 'pages_show_list', 'pages_messaging', 'pages_messaging_subscriptions']; // Optional permissions

    if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == true) {
        $time = time();
        $hash = sha1(erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['secret_hash'] . '_' . erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['address'] . '_' .  $_SERVER['HTTP_HOST'] . '_' . erLhcoreClassUser::instance()->getUserID() . $time);
        $loginUrl = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['address'] . erLhcoreClassDesign::baseurl('fbmessenger/fbloginstandalone') . '/' . $_SERVER['HTTP_HOST'] . '/' . erLhcoreClassUser::instance()->getUserID() . '/' . $time . '/' . $hash;
    } else if (!class_exists('erLhcoreClassInstance')) {
        $loginUrl = $helper->getReRequestUrl('https://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('fbmessenger/fbcallback'), $permissions);
    } else {
        $time = time();
        $hash = sha1(erConfigClassLhConfig::getInstance()->getSetting('site','seller_secret_hash',false) . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain',false) . '_' .  erLhcoreClassInstance::getInstance()->id . '_' . erLhcoreClassUser::instance()->getUserID() . $time);
        $loginUrl = 'https://'.  erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain',false) . '.' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain',false) . erLhcoreClassDesign::baseurl('fbmessenger/fblogininstance') . '/' . erLhcoreClassInstance::getInstance()->id . '/' . erLhcoreClassUser::instance()->getUserID() . '/' . $time . '/' . $hash;
    }

    echo '<a title="Log in with Facebook!" href="' . htmlspecialchars($loginUrl) . '"><img height="40" src="' . erLhcoreClassDesign::design('images/login-fb.png') .'" title="Log in with Facebook!" alt="Log in with Facebook!" /></a>';
} else {
    $logoutFB = true;

}
?>
<ul>
    <?php if (isset($logoutFB)) : ?>
        <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/myfbpages');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','My pages');?></a></li>
        <li><a class="csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/fblogout');?>" class=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Logout');?></a></li>
    <?php endif; ?>
</ul>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<hr>

<ul>
    <?php if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == false) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook pages');?></a></li>
    <?php endif; ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/bbcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','BBCode');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/leads')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Leads');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/notifications')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Notifications');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/options')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Options');?></a></li>
</ul>
