<?php $fbmessenger_module_enabled_pre = !class_exists('erLhcoreClassInstance') || erLhcoreClassInstance::getInstance()->feature_2_supported == 1;?>

<?php if ($fbmessenger_module_enabled_pre === false) : $errors[] = 'Module not supported'; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
    <?php return; endif; ?>

<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook leads list');?></h1>

<?php if (isset($items)) : ?>

    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Photo');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','User ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','E-mail');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Phone');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Gender');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Surname');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Locale');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Status');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo htmlspecialchars($item->id)?></td>
                <td>
                    <?php if ($item->profile_pic != '') : ?>
                    <img class="img-responsive" width="60" src="<?php echo $item->profile_pic_front?>" />
                    <?php else : ?>
                    N/A
                    <?php endif; ?>
                </td>
                <td><?php if ($item->source == erLhcoreClassModelFBLead::SOURCE_INSTAGRAM) : ?><img class="img-fluid me-2" src="<?php echo erLhcoreClassDesign::design('images/social/instagram-ico.png')?>"><?php else : ?><img class="img-fluid me-2" src="<?php echo erLhcoreClassDesign::design('images/social/messenger-ico.png')?>"><?php endif; ?><?php echo htmlspecialchars($item->page_id)?></td>
                <td><?php echo htmlspecialchars($item->user_id)?></td>
                <td><?php echo htmlspecialchars($item->email)?></td>
                <td><?php echo htmlspecialchars($item->phone)?></td>
                <td><?php echo ucfirst($item->gender)?></td>
                <td><?php echo htmlspecialchars($item->first_name)?></td>
                <td><?php echo htmlspecialchars($item->last_name)?></td>
                <td><?php echo htmlspecialchars($item->auto_stop)?></td>
                <td><?php echo htmlspecialchars($item->locale)?></td>
                <td><?php echo htmlspecialchars($item->dep)?></td>
                <td>
                    <?php if ($item->blocked == 1) : ?>
                        <i title="Blocked" class="material-icons chat-closed">&#xE14B;</i>
                    <?php else : ?>
                        <i title="Ok" class="material-icons chat-active">&#xE5CA;</i>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif;?>
