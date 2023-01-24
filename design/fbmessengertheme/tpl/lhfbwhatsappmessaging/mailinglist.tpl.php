<?php include(erLhcoreClassDesign::designtpl('lhfbwhatsappmessaging/parts/search_panel_mailinglist.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Members');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','User');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Created at');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Type');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php if ($item->can_edit) : ?>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/editmailinglist')?>/<?php echo $item->id?>" ><span class="material-icons">edit</span><?php echo htmlspecialchars($item->name)?></a>
                    <?php else : ?>
                        <span class="material-icons">edit_off</span><?php echo htmlspecialchars($item->name)?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/mailingrecipient')?>/(ml)/<?php echo $item->id?>" ><span class="material-icons">list</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','List of members');?> (<?php echo $item->total_contacts?>)</a>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->user); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->created_at_front); ?>
                </td>
                <td>
                    <?php if ($item->private == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::LIST_PUBLIC) : ?>
                        <span class="material-icons">public</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Public');?>
                    <?php else : ?>
                        <span class="material-icons">vpn_lock</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Private');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->can_delete) : ?>
                    <a class="text-danger csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/deletemailinglist')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>
<?php endif; ?>

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/newmailinglist')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>