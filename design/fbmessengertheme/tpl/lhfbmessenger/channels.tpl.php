<div class="row">
    <div class="col-12">
        <?php $fbmessenger_module_enabled_pre = !class_exists('erLhcoreClassInstance') || erLhcoreClassInstance::getInstance()->feature_2_supported == 1;?>

        <?php if ($fbmessenger_module_enabled_pre === false) : $errors[] = 'Module not supported'; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php return; endif; ?>

        <h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Segment Tags');?></h1>

        <?php if (isset($items)) : ?>

            <table cellpadding="0" cellspacing="0" class="table" width="100%">
                <thead>
                <tr>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/editchannel')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a></td>
                        <td><a class="btn btn-danger btn-xs csfr-required" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/deletechannel')?>/<?php echo $item->id?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

            <?php if (isset($pages)) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
            <?php endif;?>

        <?php endif;?>

        <a href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/newchannel')?>" class="btn btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','New segment');?></a>
    </div>
</div>


