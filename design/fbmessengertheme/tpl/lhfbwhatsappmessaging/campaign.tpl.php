<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Campaigns list');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhfbwhatsappmessaging/parts/search_panel_mailinglist.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Owner');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Type');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Date scheduled');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipients');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr class="<?php if ($item->status == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED) : ?>text-muted<?php endif;?>">
                <td><?php echo $item->id?></td>
                <td>
                    <a class="csfr-post text-muted csfr-required" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Copy campaign');?>" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaign')?>/(action)/copy/(id)/<?php echo $item->id?>" ><i class="material-icons mr-0">content_copy</i></a>

                    <?php if ($item->can_edit) : ?>
                    <a class="<?php if ($item->status == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED) : ?>text-muted<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/editcampaign')?>/<?php echo $item->id?>" ><span class="material-icons">edit</span><?php echo htmlspecialchars($item->name)?></a>
                    <?php else : ?>
                        <span class="material-icons">edit_off</span><?php echo htmlspecialchars($item->name)?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->user);?>
                </td>
                <td>
                    <?php if ($item->private == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::LIST_PUBLIC) : ?>
                        <span class="material-icons">public</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Public');?>
                    <?php else : ?>
                        <span class="material-icons">vpn_lock</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Private');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->enabled == 0) : ?>
                        <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Not active');?>">hourglass_disabled</span>
                    <?php else : ?>
                        <span class="material-icons" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Active');?>">hourglass_empty</span>
                    <?php endif; ?>
                    <?php if ($item->status == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_PENDING) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending');?>
                    <?php elseif ($item->status == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_IN_PROGRESS) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In progress');?>
                    <?php elseif ($item->status == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Finished');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->starts_at > 0) : ?>
                        <span class="text-<?php $item->starts_at > time() ? print 'success' : print 'warning'?>"><?php echo date('Y-m-d H:i',$item->starts_at)?></span>
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <a class="<?php if ($item->status == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED) : ?>text-muted<?php endif;?>" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaignrecipient')?>/(campaign)/<?php echo $item->id?>" ><span class="material-icons">list</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','List of recipients');?> (<?php echo $item->total_contacts?>)</a>
                </td>
                <td>
                    <?php if ($item->can_delete) : ?>
                    <a class="text-danger csfr-post csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/deletecampaign')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
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

<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/newcampaign')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>