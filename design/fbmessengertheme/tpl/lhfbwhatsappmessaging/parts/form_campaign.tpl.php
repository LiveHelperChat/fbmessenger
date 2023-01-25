<div class="row">
    <div class="col-8">

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?>*</label>
                    <input type="text" maxlength="250" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Business account');?>, <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','you can set a custom business account');?></i></small></label>
                    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                        'input_name'     => 'business_account_id',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/translation','Default configuration'),
                        'selected_id'    => $item->business_account_id,
                        'css_class'      => 'form-control form-control-sm',
                        'list_function'  => '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::getList'
                    )); ?>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?></label>
                    <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
                        'input_name'     => 'dep_id',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Select department'),
                        'selected_id'    => $item->dep_id,
                        'css_class'      => 'form-control form-control-sm',
                        'list_function'  => 'erLhcoreClassModelDepartament::getList',
                        'list_function_params'  => array('limit' => false, 'sort' => 'name ASC'),
                    )); ?>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><input type="checkbox" <?php if ($item->id == null || \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getCount(['filter' => ['status' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING, 'campaign_id' => $item->id]]) == 0) : $disabledCampaign = true;?>disabled<?php endif;?> name="enabled" value="on" <?php $item->enabled == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Activate campaign');?></label>
                    <?php if (isset($disabledCampaign) && $disabledCampaign == true) : ?><div class="text-danger"><small><i>You will be able to activate campaign once you have at-least one recipient</i></small></div><?php endif; ?>
                    <div><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Only once the campaign is activated we will start sending messages. Progress you can see in statistic tab.');?></i></small></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label class="<?php ($item->starts_at > 0 && $item->starts_at < time()) ? print 'text-danger' : ''?> "><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Start sending at');?> <b><?php print date_default_timezone_get()?></b>, Current time - <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></label>
                    <input class="form-control form-control-sm" name="starts_at" type="datetime-local" value="<?php echo date('Y-m-d\TH:i', $item->starts_at > 0 ? $item->starts_at : time())?>">
                </div>
                <?php if ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_PENDING) : ?>
                    <div class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pending, campaign has not started yet.');?></div>
                <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_IN_PROGRESS) : ?>
                    <div class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','In progress');?></div>

                    <input type="submit" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Pause a running campaign');?>" class="btn btn-xs btn-warning" name="PauseCampaign" />

                <?php elseif ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED) : ?>
                    <label><input type="checkbox" name="activate_again" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Set campaign status to pending. E.g You can activate it again if you have added more recipients.');?></label>
                <?php endif; ?>
            </div>
            <div class="col-6">
                <label><input type="checkbox" name="private" value="on" <?php $item->private == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::LIST_PRIVATE ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Private');?></label>
            </div>
        </div>

        <script>
            var messageFieldsValues = <?php echo json_encode($item->message_variables_array);?>;
            var businessAccountId = <?php echo (int)$item->business_account_id?>;
        </script>

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
                    <option <?php if ($item->template == $template['name']) : ?>selected="selected"<?php endif;?> value="<?php echo htmlspecialchars($template['name'] . '||' . $template['language'] . '||' . $template['id'])?>"><?php echo htmlspecialchars($template['name'] . ' [' . $template['language'] . ']')?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="arguments-template-form"></div>

    </div>
    <div class="col-4">
        <div id="arguments-template"></div>
    </div>
</div>

<p><small><a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/mailingcampaign'});" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Replaceable variables?');?></a></small></p>

