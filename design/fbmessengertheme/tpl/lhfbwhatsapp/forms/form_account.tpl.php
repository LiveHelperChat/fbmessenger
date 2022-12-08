<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Business Account ID');?></label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="business_account_id" value="<?php echo htmlspecialchars($item->business_account_id)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Access Token');?></label>
    <input type="text" maxlength="250" class="form-control form-control-sm" name="access_token" value="<?php echo htmlspecialchars($item->access_token)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
        'input_name'     => 'dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
        'selected_id'    => $item->dep_id,
        'css_class'      => 'form-control form-control-sm',
        'list_function'  => 'erLhcoreClassModelDepartament::getList',
        'list_function_params'  => array(),
    )); ?>
</div>

<div class="form-group">
    <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Active');?></label>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Related Phone Numbers IDs');?></label>
    <br/>
    <pre><?php echo htmlspecialchars($item->phone_number_ids); ?></pre>
    <?php if ($item->id > 0) : ?>

    <?php foreach ($item->phone_number_ids_array as $phoneNumberId) : ?>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department for');?> - <b><?php echo $phoneNumberId?></b></label>
            <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
                'input_name'     => 'dep_'.$phoneNumberId,
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
                'selected_id'    => (isset($item->phone_number_deps_array[$phoneNumberId]) ? $item->phone_number_deps_array[$phoneNumberId] : 0),
                'css_class'      => 'form-control form-control-sm',
                'list_function'  => 'erLhcoreClassModelDepartament::getList',
                'list_function_params'  => array(),
            )); ?>
        </div>
    <?php endforeach; ?>

        <input type="submit" class="btn btn-sm btn-outline-secondary" name="UpdatePhones_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update Pone Numbers');?>"/>
    <?php endif; ?>
</div>
