<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Name');?>*</label>
    <input type="text" maxlength="50" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="form-group">
    <label><input type="checkbox" name="private" <?php if ($item->private == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::LIST_PRIVATE) : ?>checked="checked"<?php endif;?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Private');?></label>
</div>


