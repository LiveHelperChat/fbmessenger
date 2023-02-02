<div role="tabpanel" class="tab-pane" id="main-extension-lang-fb-{{$index}}">
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Message');?></label>
        <textarea class="form-control" name="message_lang_fb[{{$index}}]" ng-model="lang.message_lang_fb"></textarea>
    </div>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Fallback message');?></label>
        <textarea class="form-control" name="fallback_message_lang_fb[{{$index}}]" ng-model="lang.fallback_message_lang_fb"></textarea>
    </div>
</div>