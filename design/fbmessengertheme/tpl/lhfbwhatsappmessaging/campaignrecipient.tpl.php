<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Campaign recipient');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhfbwhatsappmessaging/parts/search_panel_campaign_recipient.tpl.php')); ?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Recipient');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send at');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Type');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Read');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Chat');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->id?></td>
                <td>
                    <button data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Copied');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Click to copy phone');?>" class="mx-0 btn btn-xs btn-link text-muted py-1" data-copy="<?php echo htmlspecialchars($item->recipient_phone)?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons mr-0">content_copy</i></button>

                    <?php if ($item->message_id > 0) : ?>
                        <a class="material-icons" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'fbwhatsapp/rawjson/<?php echo $item->message_id?>'})">info_outline</a>
                    <?php endif; ?>

                    <?php if ($item->type == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::TYPE_MANUAL) : ?>

                        <?php if ($item->can_edit) : ?>
                            <button class="p-0 m-0 btn btn-sm btn-link" href="#" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/newcampaignrecipient')?>/<?php echo $campaign->id?>/<?php echo $item->id?>'})"><span class="material-icons">edit</span><?php echo htmlspecialchars($item->recipient_phone)?><?php $item->recipient_phone_recipient != '' ? print ' (' . $item->recipient_phone_recipient . ') ' : print ' '?><?php echo htmlspecialchars($item->recipient)?></button>
                        <?php else : ?>
                            <span class="material-icons">edit_off</span><?php echo htmlspecialchars($item->recipient_phone)?><?php $item->recipient_phone_recipient != '' ? print ' (' . $item->recipient_phone_recipient . ') ' : print ' '?><?php echo htmlspecialchars($item->recipient)?>
                        <?php endif; ?>

                    <?php else : ?>
                        <?php if ($item->can_edit) : ?>
                            <button class="m-0 p-0 btn btn-sm btn-link" href="#" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url': WWW_DIR_JAVASCRIPT + '/fbwhatsappmessaging/editmailingrecipient/<?php echo $item->recipient_id?>'})">
                                <span class="material-icons">edit</span><?php echo htmlspecialchars($item->recipient_phone)?><?php $item->recipient_phone_recipient != '' ? print ' (' . $item->recipient_phone_recipient . ') ' : print ' '?><?php echo htmlspecialchars($item->recipient)?>
                            </button>
                        <?php else : ?>
                            <span class="material-icons">edit_off</span><?php echo htmlspecialchars($item->recipient_phone)?><?php $item->recipient_phone_recipient != '' ? print ' (' . $item->recipient_phone_recipient . ') ' : print ' '?><?php echo htmlspecialchars($item->recipient)?>
                        <?php endif; ?>&nbsp;
                    <?php endif; ?>&nbsp;
                    <a class="csfr-required text-muted border rounded px-1" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/sendtestmessage')?>/<?php echo $item->id?>" onclick="return confirm('Are you sure?')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send test message');?></a>
                </td>
                <td>
                    <?php if ($item->send_at > 0) : ?><?php echo $item->send_at_front?><?php endif;?>
                </td>
                <td>
                    <?php if ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SCHEDULED) : ?>
                        <span class="material-icons">schedule_send</span> Scheduled
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_READ) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Read');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_DELIVERED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivered');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_IN_PROCESS) : ?>
                        <?php if ($item->mb_id_message == '') : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In process');?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Processed. Pending callback.');?>
                        <?php endif; ?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_FAILED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING_PROCESS) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending to be processed');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_REJECTED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Rejected');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SENT) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Sent');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->type == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::TYPE_MANUAL) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Manual');?>
                    <?php else : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Based on recipient list');?>
                    <?php endif; ?>
                </td>
                <td>
                    <span title="<?php if ($item->opened_at == 0) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail was not opened yet!') ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mail was opened first time at') ?> <?php echo$item->opened_at_front?><?php endif;?>" class="material-icons<?php $item->opened_at == 0 ? print ' text-muted' : print ' text-success'?>">visibility</span>
                </td>
                <td>
                    <?php if ($item->conversation_id > 0) : ?>
                        <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('front/default')?>/(cid)/<?php echo $item->conversation_id?>/#!#chat-id-<?php echo $item->conversation_id?>"><span class="material-icons">open_in_new</span><?php echo $item->conversation_id?></a>
                    <?php else : ?>
                        <span class="text-muted"><span class="material-icons">autorenew</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending');?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->can_delete) : ?>
                        <a class="text-danger csfr-required" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/deletecampaignrecipient')?>/<?php echo $item->id?>" ><i class="material-icons mr-0">&#xE872;</i></a>
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

