<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send a single message');?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($fbcommand)) : ?>
<div class="alert alert-info">
    <?php echo htmlspecialchars($fbcommand)?>
</div>
<?php endif;?>

<?php  if (isset($whatsapp_contact)) : ?>

<div class="row">
    <div class="col-6">
        <ul class="fs14">
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?> - <?php echo htmlspecialchars($whatsapp_contact->name)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Lastname');?> - <?php echo htmlspecialchars($whatsapp_contact->lastname)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','E-mail');?> - <?php echo htmlspecialchars($whatsapp_contact->email)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Title');?> - <?php echo htmlspecialchars($whatsapp_contact->title)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Company');?> - <?php echo htmlspecialchars($whatsapp_contact->company)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Date');?> - <?php echo htmlspecialchars($whatsapp_contact->date_front)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Attribute string 6');?> - <?php echo htmlspecialchars($whatsapp_contact->attr_str_6)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 1');?> - <?php echo htmlspecialchars($whatsapp_contact->file_1)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 2');?> - <?php echo htmlspecialchars($whatsapp_contact->file_2)?></li>
        </ul>
    </div>
    <div class="col-6">
        <ul class="fs14">
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Attribute string 1');?> - <?php echo htmlspecialchars($whatsapp_contact->attr_str_1)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Attribute string 2');?> - <?php echo htmlspecialchars($whatsapp_contact->attr_str_2)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Attribute string 3');?> - <?php echo htmlspecialchars($whatsapp_contact->attr_str_3)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Attribute string 4');?> - <?php echo htmlspecialchars($whatsapp_contact->attr_str_4)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Attribute string 5');?> - <?php echo htmlspecialchars($whatsapp_contact->attr_str_5)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 3');?> - <?php echo htmlspecialchars($whatsapp_contact->file_3)?></li>
            <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 4');?> - <?php echo htmlspecialchars($whatsapp_contact->file_4)?></li>
        </ul>
    </div>
</div>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('fbwhatsapp/send')?><?php if (isset($whatsapp_contact)) : ?>/(recipient)/<?php echo $whatsapp_contact->id;endif; ?>" method="post" ng-non-bindable>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <div class="row">
        <div class="col-8">

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipient Phone');?>*</label>
                        <div class="input-group input-group-sm mb-3">
                            <span class="input-group-text" id="basic-addon1">+</span>
                            <input <?php if (isset($whatsapp_contact)) : ?>disabled="disabled"<?php endif;?> type="text" name="phone" placeholder="37065111111" class="form-control" value="<?php echo htmlspecialchars((string)$send->phone)?>" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipient Phone (WhatsApp internal number)');?>*</label>
                        <div class="input-group input-group-sm mb-3">
                            <span class="input-group-text" id="basic-addon1">+</span>
                            <input type="text" <?php if (isset($whatsapp_contact)) : ?>disabled="disabled"<?php endif;?> placeholder="370865111111" name="phone_whatsapp" class="form-control" value="<?php echo htmlspecialchars((string)$send->phone_whatsapp)?>" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
            </div>

            <p>During testing it happens that test number you have to send has different number than received callback. E.g
            <ul>
                <li>If real number is 37065111111 (Recipient Phone*)</li>
                <li>In what's app send number has to be 370865111111 (Recipient Phone (WhatsApp internal number)*)</li>
            </ul>
            </p>

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
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Business account');?>, <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','you can set a custom business account');?></i></small></label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'business_account_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Default configuration'),
                    'selected_id'    => $send->business_account_id,
                    'css_class'      => 'form-control form-control-sm',
                    'list_function'  => '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::getList'
                )); ?>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Sender Phone');?></label>
                <select name="phone_sender_id" id="id_phone_sender_id" class="form-control form-control-sm" title="display_phone_number | verified_name | code_verification_status | quality_rating">
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
                <?php if (isset($business_account_id) && is_numeric($business_account_id)) : ?>
                    var businessAccountId = <?php echo (int)$business_account_id?>;
                <?php endif;?>
            </script>

            <div id="arguments-template-form"></div>

            <div class="form-group">
                <label><input onchange="$('#schedule_ts').toggle()" <?php if ($send->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED) : ?>checked="checked"<?php endif;?> type="checkbox" name="schedule_message" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Schedule a message');?>, <small class="text-muted"><?php echo date('Y-m-d H:i', time())?></small></label>
            </div>

            <div id="schedule_ts" class="pb-2" style="display:<?php if ($send->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED) : ?>block<?php else : ?>none<?php endif;?>" >
                <div class="pb-2">
                    <input type="datetime-local" class="form-control form-control-sm" name="scheduled_at" value="<?php echo date('Y-m-d\TH:i', $send->scheduled_at > 0 ? $send->scheduled_at : time())?>" />
                </div>
                
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Campaign name');?></label>
                <input type="text" class="form-control form-control-sm" maxlength="50" name="campaign_name" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Single campaign');?>" value="<?php echo htmlspecialchars((string)$send->campaign_name)?>" />
            </div>

        </div>
        <div class="col-4">
            <div id="arguments-template"></div>
        </div>
    </div>

    <button class="btn btn-secondary btn-sm" type="submit" value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Send a test message');?></button>
</form>