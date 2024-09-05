<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></label>
    <input type="text" maxlength="250" class="form-control" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page ID');?></label>
    <input type="text" maxlength="250" class="form-control" name="page_id" value="<?php echo htmlspecialchars($item->page_id)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Page token');?></label>
    <input type="text" maxlength="250" class="form-control" name="page_token" value="<?php echo htmlspecialchars($item->page_token)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Verify token');?></label>
    <input type="text" maxlength="250" class="form-control" name="verify_token" value="<?php echo htmlspecialchars($item->verify_token)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','APP secret');?></label>
    <input type="text" maxlength="250" class="form-control" name="app_secret" value="<?php echo htmlspecialchars($item->app_secret)?>" />
</div>

<div class="form-group">
    <label><input type="checkbox" name="verified" value="on" <?php $item->verified == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Application was verified by facebook');?></label>
</div>

<div class="form-group">
    <label><input type="checkbox" name="bot_disabled" value="on" <?php $item->bot_disabled == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Bot disabled');?></label>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
            'input_name'     => 'dep_id',
    		'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
            'selected_id'    => $item->dep_id,
            'css_class'      => 'form-control',
            'list_function'  => 'erLhcoreClassModelDepartament::getList',
            'list_function_params'  => array(),
    )); ?>
</div>