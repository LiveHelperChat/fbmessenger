<div class="row">
    <?php include(erLhcoreClassDesign::designtpl('lhfbbot/parts/menu_left.php')); ?>
    <div class="col-xs-10">
        <h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Edit Segment');?></h1>

        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>

        <form action="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/editchannel')?>/<?php echo $item->id?>" method="post">

            <?php include(erLhcoreClassDesign::designtpl('lhfbmessenger/parts/form_channel.tpl.php'));?>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>

        </form>
    </div>
</div>