<form action="" method="post" enctype="multipart/form-data">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="row">
        <div class="col-8">
            <?php if (isset($errors)) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php endif; ?>

            <?php if (isset($update)) : ?>
                <div role="alert" class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <ul>
                        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Imported');?> - <?php echo $update['imported']?></li>
                    </ul>
                </div>
            <?php endif; ?>

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

            <div class="form-group">
                <label>CSV</label>
                <input type="file" name="files" />
            </div>

            <script>
                var messageFieldsValues = <?php echo json_encode($send->message_variables_array);?>;
            </script>

            <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','First row in CSV is skipped. Columns order');?> - </small>
                <span class="badge badge-secondary mr-2">phone</span>
                <span class="badge badge-secondary mr-2">field_1</span>
                <span class="badge badge-secondary mr-2">field_2</span>
                <span class="badge badge-secondary mr-2">field_3</span>
                <span class="badge badge-secondary mr-2">field_4</span>
                <span class="badge badge-secondary mr-2">field_5</span>
                <span class="badge badge-secondary">field_6</span>
            </p>

            <input type="submit" class="btn btn-sm btn-secondary" name="UploadFileAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Import and start sending');?>" />
        </div>
        <div class="col-4">
            <div id="arguments-template"></div>
        </div>
    </div>
</form>