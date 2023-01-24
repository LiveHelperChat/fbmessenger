<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Phone');?></label>
            <div class="input-group input-group-sm mb-3">
                <span class="input-group-text" id="basic-addon1">+</span>
                <input type="text" maxlength="50" class="form-control form-control-sm" name="phone" value="<?php echo htmlspecialchars($item->phone)?>" />
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
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Date');?>, <b><?php print date_default_timezone_get()?></b>, Current time - <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></label>
            <input class="form-control form-control-sm" name="date" type="datetime-local" value="<?php echo $item->date > 0 ? date('Y-m-d\TH:i', $item->date) : ''?>">
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 1');?>. {args.recipient.file_1_url_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="file_1" value="<?php echo htmlspecialchars($item->file_1)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 2');?>. {args.recipient.file_2_url_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="file_2" value="<?php echo htmlspecialchars($item->file_2)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 3');?>. {args.recipient.file_3_url_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="file_3" value="<?php echo htmlspecialchars($item->file_3)?>" />
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','File 4');?>. {args.recipient.file_4_url_front}</label>
            <input type="text" maxlength="50" class="form-control form-control-sm" name="file_4" value="<?php echo htmlspecialchars($item->file_4)?>" />
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