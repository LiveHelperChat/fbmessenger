<div class="row">
    <div class="col-12">
        <h1>New segment</h1>

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <form action="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/newchannel')?>" method="post">

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></label>
                <input type="text" maxlength="250" class="form-control" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
            </div>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>

        </form>
    </div>
</div>