<form action="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/whatsappregistercloudapi')?>/<?php echo $phoneNumber['business_id']?>/<?php echo $phoneNumber['whatsapp_business_account_id']?>/<?php echo $phoneNumber['id']?>" method="post" ng-non-bindable>

    <p>This step is required only if you are adding a personal phone number to WhatsApp business account.</p>

    <?php if (isset($errors)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php endif; ?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Registered'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <h3 class="attr-header">Register in Cloud-API</h3>

    <div class="form-group">
        <label>Phone Number ID, filled automatically.</label>
        <div class="text-muted"><?php echo htmlspecialchars($phoneNumber['id'])?></div>
    </div>

    <div class="form-group">
        <label>PIN. Required if you are using 2 factor authenticator. If you don't know what to enter just enter 123456</label>
        <input type="text" class="form-control" placeholder="PIN" name="pin" value="<?php echo htmlspecialchars($phoneNumber['pin'])?>">
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Register'); ?>" />&nbsp;

</form>
