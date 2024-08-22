<?php $fbmessenger_module_enabled_pre = !class_exists('erLhcoreClassInstance') || erLhcoreClassInstance::getInstance()->feature_2_supported == 1;?>

<?php if ($fbmessenger_module_enabled_pre === false) : $errors[] = 'Module not supported'; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php return; endif; ?>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook messenger');?></h1>

<ul>
    <?php

        $fb = erLhcoreClassModelFBMessengerUser::getFBAppInstance();

        $helper = $fb->getRedirectLoginHelper();
    
        $permissions = ['email', 'pages_show_list', 'pages_messaging', 'instagram_manage_messages', 'instagram_basic', 'pages_manage_metadata']; // Optional permissions

        $time = time();
        $hash = sha1(erConfigClassLhConfig::getInstance()->getSetting('site','seller_secret_hash') . '_' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '_' .  erLhcoreClassInstance::getInstance()->id . '_' . erLhcoreClassUser::instance()->getUserID() . $time);
        $loginUrl = 'https://'.  erConfigClassLhConfig::getInstance()->getSetting('site','seller_subdomain') . '.' . erConfigClassLhConfig::getInstance()->getSetting('site','seller_domain') . erLhcoreClassDesign::baseurl('fbmessenger/fblogininstanceinstagram') . '/' . erLhcoreClassInstance::getInstance()->id . '/' . erLhcoreClassUser::instance()->getUserID() . '/' . $time . '/' . $hash;

        echo '<li><a href="' . htmlspecialchars($loginUrl) . '"><img height="30" src="'. erLhcoreClassDesign::design('images/fblogin.png') .'" /></a></li>';
    ?>

    <li><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/bbcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','BBCode');?></a></li>
</ul>


<table class="table">
    <thead>
    <th colspan="2">Page</th>
    </thead>
    <?php /*foreach ($pages['data'] as $page) : ?>

    <?php endforeach;*/ ?>
</table>