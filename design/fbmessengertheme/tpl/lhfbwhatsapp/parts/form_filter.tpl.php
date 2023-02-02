<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Phone');?></label>
                <input type="text" class="form-control form-control-sm" name="phone" value="<?php echo htmlspecialchars($input->phone)?>" />
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Template');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'template_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Any'),
                    'display_name'   => 'name',
                    'selected_id'    => $input->template_ids,
                    'css_class'      => 'form-control form-control-sm',
                    'list_function'  => '\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::getTemplates'
                )); ?>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Phone sender');?></label>
                <input type="text" class="form-control form-control-sm" name="phone_sender" value="<?php echo htmlspecialchars($input->phone_sender)?>" />
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send status');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'status_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Any'),
                    'display_name'   => 'name',
                    'selected_id'    => $input->status_ids,
                    'css_class'      => 'form-control form-control-sm',
                    'list_function'  => '\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::getStatus'
                )); ?>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Business account');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'business_account_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Any'),
                    'display_name'   => 'name',
                    'selected_id'    => $input->business_account_ids,
                    'css_class'      => 'form-control form-control-sm',
                    'list_function'  => '\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::getList'
                )); ?>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','User');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_ids[]',
                    'optional_field' => 'Choose a user',
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
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Campaign');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'campaign_ids[]',
                    'optional_field' => 'Choose a campaign',
                    'selected_id'    => $input->campaign_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => array('sort' => '`name` ASC', 'limit' => 50),
                    'list_function'  => 'LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::getList',
                )); ?>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_ids,
                    'ajax'           => 'deps',
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 50],erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="timefrom_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timefrom_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timefrom_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_seconds) && $input->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="timeto_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timeto_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timeto_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="btn-group mr-2" role="group" aria-label="...">
        <button type="submit" class="btn btn-primary btn-sm" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Search');?></button>
        <?php if ($pages->items_total > 0) : ?>
        <a href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsapp/messages')?>/(export)/csv<?php echo $inputAppend?>" class="btn btn-outline-secondary btn-sm">
            <i class="material-icons">file_download</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Export');?> (<?php echo $pages->items_total?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','messages');?>)
        </a>

        <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/stats?<?php echo $inputAppend?>'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">query_stats</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Quick stats')?></button>

        <?php endif; ?>
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsapp/messages')?>"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
    </div>

</form>

<script>
    $(function() {
        $('#id_timefrom,#id_timeto').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('.btn-block-department').makeDropdown();
    });
</script>