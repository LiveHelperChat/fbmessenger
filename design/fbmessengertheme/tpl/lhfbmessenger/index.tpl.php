<?php $fbmessenger_module_enabled_pre = !class_exists('erLhcoreClassInstance') || erLhcoreClassInstance::getInstance()->feature_2_supported == 1;?>

<?php if ($fbmessenger_module_enabled_pre === false) : $errors[] = 'Module not supported'; ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php return; endif; ?>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook messenger');?></h1>

<ul>
    <?php /*<li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/bot')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Bot setup');?></a></li>*/ ?>


    <?php
        $user = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())));

        if (!($user instanceof erLhcoreClassModelFBMessengerUser)) {
            $fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();

            $helper = $fb->getRedirectLoginHelper();

            $permissions = ['email', 'pages_show_list', 'pages_messaging', 'instagram_manage_messages', 'instagram_basic', 'pages_manage_metadata']; // Optional permissions

            //$loginUrl = $helper->getReRequestUrl('https://'.$_SERVER['HTTP_HOST']. erLhcoreClassDesign::baseurl('fbmessenger/fbcallback'), $permissions);

            $time = time();
            $hash = sha1(erConfigClassLhConfig::getInstance()->getSetting('site','seller_secret_hash') . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '_' .  erLhcoreClassInstance::getInstance()->id . '_' . erLhcoreClassUser::instance()->getUserID() . $time);
            $loginUrl = 'https://'.  erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '.' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain') . erLhcoreClassDesign::baseurl('fbmessenger/fblogininstance') . '/' . erLhcoreClassInstance::getInstance()->id . '/' . erLhcoreClassUser::instance()->getUserID() . '/' . $time . '/' . $hash;
                        
            echo '<li><a href="' . htmlspecialchars($loginUrl) . '"><img height="30" src="/extension/fbmessenger/design/fbmessengertheme/images/fblogin.png" /></a></li>';
        } else {
            echo '
            <li><a href="' . erLhcoreClassDesign::baseurl('fbmessenger/myfbpages') . '">My pages</a></li>
            <li><a href="' . erLhcoreClassDesign::baseurl('fbmessenger/fblogout') . '">Logout</a></li>';
        }
    ?>

    <li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbot/chatbot')?>?page=chatbot-builder"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chatbot');?></a></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/bbcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','BBCode');?></a></li>
</ul>