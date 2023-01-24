<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Messages history');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhfbwhatsapp/parts/form_filter.tpl.php'));?>

<?php if (isset($items)) : ?>
    <table cellpadding="0" cellspacing="0" class="table table-sm" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Business Account');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Campaign');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Type');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Date');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Template');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','User');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Phone');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send status');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Scheduled at');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Chat ID');?></th>
            <th width="1%"></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td nowrap="" title="<?php echo date(erLhcoreClassModule::$dateDateHourFormat,$item->created_at);?> | <?php echo htmlspecialchars($item->fb_msg_id) ?> | <?php echo htmlspecialchars($item->conversation_id) ?>">
                    <?php echo htmlspecialchars($item->id) ?> <a class="material-icons" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'fbwhatsapp/rawjson/<?php echo $item->id?>'})">info_outline</a>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->business_account)?>
                </td>
                <td>
                    <?php if ($item->campaign_id > 0) : ?>
                        <a href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaignrecipient')?>/(campaign)/<?php echo htmlspecialchars($item->campaign_id) ?>"><?php echo htmlspecialchars((string)$item->campaign)?></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->private == LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::LIST_PUBLIC) : ?>
                        <span class="material-icons">public</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Public');?>
                    <?php else : ?>
                        <span class="material-icons">vpn_lock</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Private');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->department)?>
                </td>
                <td title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Updated');?> <?php echo $item->updated_at_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','ago');?>">
                    <?php echo $item->created_at_front?>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->template)?>
                </td>
                <td>
                    <?php if ($item->user_id > 0) : ?>
                        <?php echo htmlspecialchars((string)$item->user)?>
                    <?php elseif ($item->user_id == -1) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','System');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo htmlspecialchars((string)$item->phone)?>
                </td>
                <td>
                    <?php if ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED) : ?>
                        <span class="material-icons">schedule_send</span> Scheduled
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Read');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_DELIVERED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivered');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_IN_PROCESS) : ?>
                        <?php if ($item->mb_id_message == '') : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In process');?>
                        <?php else : ?>
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Processed. Pending callback.');?>
                        <?php endif; ?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_FAILED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING_PROCESS) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending to be processed');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_REJECTED) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Rejected');?>
                    <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SENT) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Sent');?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->scheduled_at > 0) : ?>
                        <span class="text-<?php $item->scheduled_at > time() ? print 'success' : print 'warning'?>"><?php echo date('Y-m-d H:i',$item->scheduled_at)?></span>
                    <?php else : ?>
                    -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($item->conversation_id != '') : ?>
                        <span class="material-icons" title=" <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Covnersation');?> - <?php echo $item->conversation_id?>">question_answer</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($item->chat_id)?>
                </td>
                <?php /*
                <td>
                    <?php if ($item->initiation == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::INIT_US) : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','We');?>
                    <?php else : ?>
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Third party');?>
                    <?php endif; ?>
                </td>*/ ?>
                <td>
                    <?php if ($item->can_delete) : ?>
                        <a class="csfr-required csfr-post material-icons text-danger" data-trans="delete_confirm" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delete message');?>" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsapp/deletemessage')?>/<?php echo htmlspecialchars($item->id) ?>">delete</a>
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