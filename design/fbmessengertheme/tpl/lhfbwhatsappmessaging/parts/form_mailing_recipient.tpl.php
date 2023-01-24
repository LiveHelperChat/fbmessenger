<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Phone');?>*</label>
            <div class="input-group input-group-sm mb-3">
                <span class="input-group-text" id="basic-addon1">+</span>
                <input placeholder="37065111111" type="text" maxlength="50" class="form-control form-control-sm" name="phone" value="<?php echo htmlspecialchars($item->phone)?>" />
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipient Phone (WhatsApp internal number)');?></label>
            <div class="input-group input-group-sm mb-3">
                <span class="input-group-text" id="basic-addon1">+</span>
                <input placeholder="370865111111" type="text" maxlength="50" class="form-control form-control-sm" name="phone_recipient" value="<?php echo htmlspecialchars($item->phone_recipient)?>" />
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?>. {args.recipient.name_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Lastname');?>. {args.recipient.lastname_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="lastname" value="<?php echo htmlspecialchars($item->lastname)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Company');?>. {args.recipient.company_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="company" value="<?php echo htmlspecialchars($item->company)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Title');?>. {args.recipient.title_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item->title)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Delivery status');?></label>
            <select class="form-control form-control-sm" name="delivery_status">
                <option value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNKNOWN?>" <?php if ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNKNOWN) : ?>selected="selected"<?php endif; ?> >Unknown</option>
                <option value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNSUBSCRIBED?>" <?php if ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNSUBSCRIBED) : ?>selected="selected"<?php endif; ?>>Unsubscribed</option>
                <option value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED?>" <?php if ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED) : ?>selected="selected"<?php endif; ?>>Failed</option>
                <option value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_ACTIVE?>" <?php if ($item->delivery_status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_ACTIVE) : ?>selected="selected"<?php endif; ?>>Active</option>
            </select>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Date');?>, <b><?php print date_default_timezone_get()?></b>, Current time - <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></label>
            <input class="form-control form-control-sm" name="date" type="datetime-local" value="<?php echo $item->date > 0 ? date('Y-m-d\TH:i', $item->date) : ''?>">
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 1');?>. {args.recipient.file_1_url_front}<button type="button" data-selector="#id_file_1" class="fb-choose-file btn btn-sm btn-link"><span class="material-icons">attach_file</span></button></label>
            <input type="text" maxlength="200" class="form-control form-control-sm" id="id_file_1" name="file_1" value="<?php echo htmlspecialchars($item->file_1)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 2');?>. {args.recipient.file_2_url_front}}<button type="button" data-selector="#id_file_2" class="fb-choose-file btn btn-sm btn-link"><span class="material-icons">attach_file</span></button></label>
            <input type="text" maxlength="200" class="form-control form-control-sm" id="id_file_2" name="file_2" value="<?php echo htmlspecialchars($item->file_2)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 3');?>. {args.recipient.file_3_url_front}}<button type="button" data-selector="#id_file_3" class="fb-choose-file btn btn-sm btn-link"><span class="material-icons">attach_file</span></button></label>
            <input type="text" maxlength="200" class="form-control form-control-sm" id="id_file_3" name="file_3" value="<?php echo htmlspecialchars($item->file_3)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 4');?>. {args.recipient.file_4_url_front}}<button type="button" data-selector="#id_file_4" class="fb-choose-file btn btn-sm btn-link"><span class="material-icons">attach_file</span></button></label>
            <input type="text" maxlength="200" class="form-control form-control-sm" id="id_file_4" name="file_4" value="<?php echo htmlspecialchars($item->file_4)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','E-mail');?>. {args.recipient.email_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($item->email)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','String attribute 1');?>. {args.recipient.attr_str_1_front}</label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="attr_str_1" value="<?php echo htmlspecialchars($item->attr_str_1)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','String attribute 2');?>. {args.recipient.attr_str_2_front}</label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="attr_str_2" value="<?php echo htmlspecialchars($item->attr_str_2)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','String attribute 3');?>. {args.recipient.attr_str_3_front}</label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="attr_str_3" value="<?php echo htmlspecialchars($item->attr_str_3)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','String attribute 4');?>. {args.recipient.attr_str_4_front}</label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="attr_str_4" value="<?php echo htmlspecialchars($item->attr_str_4)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','String attribute 5');?>. {args.recipient.attr_str_5_front}</label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="attr_str_5" value="<?php echo htmlspecialchars($item->attr_str_5)?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','String attribute 6');?>. {args.recipient.attr_str_6_front}</label>
            <input type="text" maxlength="100" class="form-control form-control-sm" name="attr_str_6" value="<?php echo htmlspecialchars($item->attr_str_6)?>" />
        </div>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','This recipient is a member of these mailing lists');?></label>
    <div class="row" style="max-height: 500px; overflow: auto">
        <?php
            $params = array (
                'input_name'     => 'ml[]',
                'display_name'   => 'name',
                'css_class'      => 'form-control form-control-sm',
                'multiple'       => true,
                'wrap_prepend'   => '<div class="col-4">',
                'wrap_append'    => '</div>',
                'selected_id'    => $item->ml_ids_front,
                'list_function'  => 'LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::getList',
                'list_function_params'  => array('sort' => '`name` ASC, `id` ASC', 'limit' => false)
            );
            echo erLhcoreClassRenderHelper::renderCheckbox( $params );
        ?>
    </div>
</div>

<hr>

<div class="form-group">
    <label><input type="checkbox" name="disabled" value="on" <?php $item->disabled == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Disabled');?></label>
</div>

<script>
    (function() {
        $('.fb-choose-file').click(function(){
            $('.embed-into').removeClass('embed-into');
            window.lhcSelector = $(this).attr('data-selector');
            $(window.lhcSelector).addClass('embed-into');
            var popupWindow = window.open(WWW_DIR_JAVASCRIPT + 'file/attatchfileimg/(replace)/1','mailrecipientfile',"menubar=1,resizable=1,width=800,height=650");
            if (popupWindow !== null) {
                popupWindow.focus();
            }
        });
    })();
</script>