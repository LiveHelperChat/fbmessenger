<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></label>
    <input type="text" maxlength="250" class="form-control" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>