<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipient list');?></h1>

<?php include(erLhcoreClassDesign::designtpl('lhfbwhatsappmessaging/parts/search_panel_mailing_recipient.tpl.php')); ?>

<table cellpadding="0" cellspacing="0" class="table table-sm table-hover" width="100%" ng-non-bindable>
    <thead>
    <tr>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','E-mail');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Date creation');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Owner');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivery status');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Type');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Chat ID');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send');?></th>
        <th width="1%"></th>
    </tr>
    </thead>
    <?php if (isset($items)) : foreach ($items as $item) : ?>
        <tr class="<?php if ($item->disabled == 1) : ?>text-muted<?php endif; ?>">
            <td>
                <i class="material-icons"><?php if ($item->disabled == 0) : ?>done<?php else : ?>block<?php endif; ?></i>
                <button data-success="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Copied');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Click to copy phone');?>" class="mx-0 btn btn-xs btn-link text-muted py-1" data-copy="<?php echo htmlspecialchars($item->phone)?>" onclick="lhinst.copyContent($(this))" type="button"><i class="material-icons mr-0">content_copy</i></button>

                <?php if ($item->can_edit) : ?>
                    <button class="m-0 p-0 btn btn-sm btn-link<?php if ($item->disabled == 1) : ?> text-muted<?php endif; ?>" href="#" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/editmailingrecipient')?>/<?php echo $item->id?>'})">
                        <span class="material-icons">edit</span><?php echo htmlspecialchars($item->phone)?><?php $item->phone_recipient != '' ? print ' (' . $item->phone_recipient . ') ' : print ' '?><?php echo htmlspecialchars($item->email)?>
                    </button>
                <?php else : ?>
                    <span class="material-icons">edit_off</span><?php echo htmlspecialchars($item->phone)?><?php $item->phone_recipient != '' ? print ' (' . $item->phone_recipient . ') ' : print ' '?><?php echo htmlspecialchars($item->email)?>
                <?php endif; ?>

            </td>
            <td>
                <?php echo htmlspecialchars($item->name)?>
            </td>
            <td>
                <?php echo htmlspecialchars($item->created_at_front)?>
            </td>
            <td>
                <?php echo htmlspecialchars((string)$item->user)?>
            </td>
            <td>
                <?php if ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNKNOWN) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Unknown');?>
                <?php elseif ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNSUBSCRIBED) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Unsubscribed');?>
                <?php elseif ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed');?>
                <?php elseif ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_ACTIVE) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Active');?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item->private == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::LIST_PUBLIC) : ?>
                    <span class="material-icons">public</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Public');?>
                <?php else : ?>
                    <span class="material-icons">vpn_lock</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Private');?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item->chat_id > 0) : ?>
                    <a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('front/default')?>/(cid)/<?php echo $item->chat_id?>/#!#chat-id-<?php echo $item->chat_id?>"><span class="material-icons">open_in_new</span><?php echo $item->chat_id?></a>
                <?php endif; ?>
            </td>
            <td>
                <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send a single message');?>" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsapp/send')?>/(recipient)/<?php echo $item->id?>"><span class="material-icons">send</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send a message');?></a>
            </td>
            <td>
                <?php if ($item->can_delete) : ?>
                <a class="csfr-required text-danger" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/messages','Are you sure?');?>')" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/deleterecipient')?>/<?php echo $item->id?><?php echo !empty($input->ml) ? '/(ml)/' . implode('/',$input->ml) : ''?>" ><i class="material-icons mr-0">&#xE872;</i></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach;endif; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>