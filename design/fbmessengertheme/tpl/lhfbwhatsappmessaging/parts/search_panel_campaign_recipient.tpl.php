<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="pb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Recipient status');?></label>
                <select name="status" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Choose');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SENT) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SENT?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_IN_PROCESS) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_IN_PROCESS?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','In progress');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_FAILED) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_FAILED?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Failed');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_REJECTED) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_REJECTED?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Rejected');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_READ) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_READ?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Read');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SCHEDULED) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SCHEDULED?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Scheduled');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_DELIVERED) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_DELIVERED?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Delivered');?></option>
                    <option <?php if ($input->status === \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING_PROCESS) : ?>selected="selected"<?php endif; ?> value="<?php echo \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING_PROCESS?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending process');?></option>
                </select>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Message read');?></label>
                <select name="opened" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Choose');?></option>
                    <option <?php if ($input->opened === 1) : ?>selected="selected"<?php endif; ?> value="1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Yes');?></option>
                    <option <?php if ($input->opened === 0) : ?>selected="selected"<?php endif; ?> value="0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','No');?></option>
                </select>
            </div>
        </div>
    </div>


    <input type="hidden" name="campaign" value="<?php echo $campaign->id?>">

    <div class="btn-group mr-2" role="group" aria-label="...">
        <button type="submit" class="btn btn-primary btn-sm" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Search');?></button>
    </div>

    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-secondary btn-sm"  onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/newcampaignrecipient')?>/<?php echo $campaign->id?>'})">
            <i class="material-icons">add</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New manual recipient');?>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="return lhc.revealModal({'title' : 'Import', 'height':350, backdrop:true, 'url':'<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/importfrommailinglist')?>/<?php echo $campaign->id?>'})" >
            <i class="material-icons">playlist_add</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update recipients from mailing list');?>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="return lhc.revealModal({'title' : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Import');?>', 'iframe':true, 'height':500, 'url':WWW_DIR_JAVASCRIPT +'fbwhatsappmessaging/importcampaign/<?php echo $campaign->id?>'})" >
            <i class="material-icons">file_upload</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Import');?>
        </button>
        <a href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaignrecipient')?>/(export)/csv<?php echo $inputAppend?>" class="btn btn-outline-secondary btn-sm">
            <i class="material-icons">file_download</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Export');?>
        </a>
    </div>

    <div role="alert" class="alert alert-info alert-dismissible hide m-3" id="list-update-import">
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','This list was updated. Please');?>&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaignrecipient')?>/(campaign)/<?php echo $campaign->id?>?refresh=<?php echo time()?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','refresh');?>.</a>
    </div>

</form>




