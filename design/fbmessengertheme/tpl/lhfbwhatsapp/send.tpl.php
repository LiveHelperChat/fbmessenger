<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send a single message');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipient Phone');?>*</label>
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">+</span>
                    </div>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars((string)$send->phone)?>" aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?>*</label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
                    'input_name'     => 'dep_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Select department'),
                    'selected_id'    => $send->dep_id,
                    'css_class'      => 'form-control form-control-sm',
                    'list_function'  => 'erLhcoreClassModelDepartament::getList',
                    'list_function_params'  => array('limit' => false, 'sort' => 'name ASC'),
                )); ?>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Sender Phone');?></label>
                <select name="phone_sender_id" class="form-control form-control-sm" title="display_phone_number | verified_name | code_verification_status | quality_rating">
                    <?php foreach ($phones as $phone) : ?>
                    <option value="<?php echo $phone['id']?>" >
                        <?php echo $phone['display_phone_number'],' | ', $phone['verified_name'],' | ', $phone['code_verification_status'],' | ', $phone['quality_rating']?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Template');?>*</label>
                <select name="template" class="form-control form-control-sm" id="template-to-send">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Choose a template');?></option>
                    <?php foreach ($templates as $template) : ?>
                        <option <?php if ($send->template == $template['name']) : ?>selected="selected"<?php endif;?> value="<?php echo htmlspecialchars($template['name'] . '||' . $template['language'] . '||' . $template['id'])?>"><?php echo htmlspecialchars($template['name'] . ' [' . $template['language'] . ']')?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <script>
                var messageFieldsValues = <?php echo json_encode($send->message_variables_array);?>;
            </script>

            <div id="arguments-template-form"></div>
        </div>
        <div class="col-4">
            <div id="arguments-template"></div>
        </div>
    </div>

    <button class="btn btn-secondary btn-sm" type="submit" value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send a test message');?></button>
</form>