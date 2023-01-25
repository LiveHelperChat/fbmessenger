<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Recipient list');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'ml[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose recipient list'),
                    'selected_id'    => $input->ml,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::limitContactList(),
                    'list_function'  => '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::getList'
                )); ?>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
                <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($input->name)?>" />
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Owner');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose owner'),
                    'selected_id'    => $input->user_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name_official',
                    'ajax'           => 'users',
                    'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC', 'limit' => 50)),
                    'list_function'  => 'erLhcoreClassModelUser::getUserList',
                )); ?>
            </div>
        </div>

        <div class="col-3">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Delivery status');?></label>
            <select name="delivery_status" class="form-control form-control-sm">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Choose');?></option>
                <option <?php if ($input->delivery_status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNKNOWN) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNKNOWN?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Unknown');?></option>
                <option <?php if ($input->delivery_status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNSUBSCRIBED) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNSUBSCRIBED?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Unsubscribed');?></option>
                <option <?php if ($input->delivery_status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Failed');?></option>
                <option <?php if ($input->delivery_status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_ACTIVE) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_ACTIVE?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Active');?></option>
            </select>
        </div>

    </div>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
        <button type="button" class="btn btn-outline-secondary btn-sm"  onclick="return lhc.revealModal({'title' : 'New', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/newmailingrecipient')?><?php if (!empty($input->ml)) : ?>/(ml)/<?php echo implode('/',$input->ml)?><?php endif;?>'})"><i class="material-icons">add</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></button>
        <a target="_blank" class="btn btn-outline-secondary btn-sm" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'fbwhatsappmessaging/import<?php if (!empty($input->ml)) : ?>/(ml)/<?php echo implode('/',$input->ml)?><?php endif;?>'})">
            <i class="material-icons">file_upload</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>
        </a>
    </div>

    <script>
        $(function() {
            $('.btn-block-department').makeDropdown();
        });
    </script>

    <div role="alert" class="alert alert-info alert-dismissible hide m-3" id="list-update-import">
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','This list was updated. Please');?>&nbsp;<a href="?refresh=<?php echo time()?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','refresh');?>.</a>
    </div>

</form>


