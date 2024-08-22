<div class="row">
    <div class="col-12">
        <?php $fbmessenger_module_enabled_pre = !class_exists('erLhcoreClassInstance') || erLhcoreClassInstance::getInstance()->feature_2_supported == 1;?>
        
        <?php if ($fbmessenger_module_enabled_pre === false) : $errors[] = 'Module not supported'; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php return; endif; ?>

        <h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook leads list');?></h1>

        <?php if (isset($items)) : ?>

            <table cellpadding="0" cellspacing="0" class="table" width="100%">
                <thead>
                <tr>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Photo');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook User ID');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Gender');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Surname');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Subscribe');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Status');?></th>
                    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Locale');?></th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td>
                            <?php if ($item->profile_pic == '') : ?>
                            N/A
                            <?php else : ?>
                            <img class="img-responsive" width="60" src="<?php echo $item->profile_pic_front?>" />
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item->user_id?></td>
                        <td><?php echo ucfirst($item->gender)?></td>
                        <td><?php echo htmlspecialchars($item->first_name)?></td>
                        <td><?php echo htmlspecialchars($item->last_name)?></td>
                        <td><?php echo htmlspecialchars($item->subscribe_channels)?></td>
                        <td><?php echo htmlspecialchars($item->auto_stop)?></td>
                        <td><?php echo $item->locale?></td>
                        <td><a class="btn btn-danger btn-xs csfr-required" onclick="return confirm('Are you sure you want to delete this record?')" href="<?php echo erLhcoreClassDesign::baseurl('fbmessenger/deletelead')?>/<?php echo $item->id?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

            <?php if (isset($pages)) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
            <?php endif;?>

        <?php endif;?>
    </div>
</div>
