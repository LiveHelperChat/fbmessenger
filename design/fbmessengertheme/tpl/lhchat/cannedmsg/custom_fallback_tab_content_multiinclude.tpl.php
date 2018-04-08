<div role="tabpanel" class="tab-pane" id="main-extension-fb">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
        <textarea class="form-control" name="MessageExtFB"><?php echo isset($canned_message->additional_data_array['message_fb']) ? $canned_message->additional_data_array['message_fb'] : '';?></textarea>
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
        <textarea class="form-control" name="FallbackMessageExtFB"><?php echo isset($canned_message->additional_data_array['fallback_fb']) ? $canned_message->additional_data_array['fallback_fb'] : '';?></textarea>
    </div>
</div>