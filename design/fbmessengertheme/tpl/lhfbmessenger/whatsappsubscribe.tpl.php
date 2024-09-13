<h1>WhatsApp subscription</h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($subscribed) && $subscribed == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','You have successfully subscribed'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($unsubscribed) && $unsubscribed == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','You have successfully un-subscribed'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($subscribed) && $subscribed == false) : $errors = array('Subscription failed')?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<a class="btn btn-secondary" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/myfbpages')?>">My pages</a>
