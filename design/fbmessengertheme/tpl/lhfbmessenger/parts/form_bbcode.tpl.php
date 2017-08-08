<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></label>
    <input type="text" maxlength="250" class="form-control" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','BBCode');?></label>
    <input type="text" maxlength="250" class="form-control" name="bbcode" value="<?php echo htmlspecialchars($item->bbcode)?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Choose button type');?></label>
    <select id="template-selector" name="bbcode_button_type" onchange="changeTemplate($(this))" class="form-control">
        <option value="web_button">Web button</option>
        <option value="web_button_generic" <?php isset($item->configuration_array['bbcode_button_type']) && $item->configuration_array['bbcode_button_type'] == 'web_button_generic' ? print 'selected="selected"' : ''?> >Web button generic</option>
        <option value="web_button_ellist" <?php isset($item->configuration_array['bbcode_button_type']) && $item->configuration_array['bbcode_button_type'] == 'web_button_ellist' ? print 'selected="selected"' : ''?> >Web element list</option>
    </select>
</div>

<div class="form-group button-template" id="button-type-web_button" style="display: none">

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Message');?></label>
        <input type="text" maxlength="250" class="form-control" name="web_button_message" value="<?php isset($item->configuration_array['web_button']['web_button_message']) ? print htmlspecialchars($item->configuration_array['web_button']['web_button_message']) : ''?>" />
    </div>

    <div class="row">
        <?php for ($i = 1; $i <= 3;$i++) : ?>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Title');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_button_web_title_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button']['web_button_web_title_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button']['web_button_web_title_' . $i]) : ''?>" />
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','URL');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_button_web_url_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button']['web_button_web_url_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button']['web_button_web_url_' . $i]) : ''?>" />
            </div>
        </div>
        <?php endfor; ?>
    </div>

</div>

<div class="form-group button-template" id="button-type-web_button_ellist" style="display: none">
    <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','At least two elements has to be filled.')?></p>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Button Title');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_button_default_web_title" value="<?php isset($item->configuration_array['web_button_list']['web_list_button_default_web_title']) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_button_default_web_title']) : ''?>" />
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Button URL');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_button_default_web_url" value="<?php isset($item->configuration_array['web_button_list']['web_list_button_default_web_url']) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_button_default_web_url']) : ''?>" />
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <?php for ($i = 1; $i <= 4;$i++) : ?>
        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Title');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_title_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_list']['web_list_title_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_title_' . $i]) : ''?>" />
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','URL');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_sub_title_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_list']['web_list_sub_title_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_sub_title_' . $i]) : ''?>" />
            </div>
        </div>

        <div class="col-xs-12">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Image URL');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_sub_img_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_list']['web_list_sub_img_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_sub_img_' . $i]) : ''?>" />
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Button Title');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_button_web_title_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_list']['web_list_button_web_title_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_button_web_title_' . $i]) : ''?>" />
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Button URL');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_button_web_url_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_list']['web_list_button_web_url_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_button_web_url_' . $i]) : ''?>" />
            </div>
        </div>

        <div class="col-xs-12">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Default URL');?></label>
                <input type="text" maxlength="250" class="form-control" name="web_list_def_url_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_list']['web_list_def_url_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_list']['web_list_def_url_' . $i]) : ''?>" />
            </div>
            <hr>
        </div>
        <?php endfor; ?>
    </div>

</div>




<div class="form-group button-template" id="button-type-web_button_generic" style="display: none">
    <div class="row">
        <?php for ($i = 1; $i <= 10; $i++) : ?>
            <div class="col-xs-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Bubble title');?>*</label>
                    <input type="text" maxlength="80" class="form-control" name="web_gen_button_title_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_gen']['web_gen_button_title_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_gen']['web_gen_button_title_' . $i]) : ''?>" />
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Bubble subtitle');?></label>
                    <input type="text" maxlength="80" class="form-control" name="web_gen_button_subtitle_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_gen']['web_gen_button_subtitle_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_gen']['web_gen_button_subtitle_' . $i]) : ''?>" />
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Bubble image');?></label>
                    <input type="text" maxlength="250" class="form-control" name="web_gen_button_img_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_gen']['web_gen_button_img_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_gen']['web_gen_button_img_' . $i]) : ''?>" />
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Default URL');?></label>
                    <input type="text" maxlength="250" class="form-control" name="web_gen_button_def_url_<?php echo $i?>" value="<?php isset($item->configuration_array['web_button_gen']['web_gen_button_def_url_' . $i]) ? print htmlspecialchars($item->configuration_array['web_button_gen']['web_gen_button_def_url_' . $i]) : ''?>" />
                </div>
            </div>

            <div class="col-xs-12">
                <h4>Buttons</h4>
                <div class="row">
                    <?php for ($n = 1; $n <= 3; $n++) : ?>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Title');?></label>
                                <input type="text" maxlength="250" class="form-control" name="web_button_web_title_<?php echo $i?>_<?php echo $n?>" value="<?php isset($item->configuration_array['web_button_gen']['web_button_web_title_' . $i . '_' . $n]) ? print htmlspecialchars($item->configuration_array['web_button_gen']['web_button_web_title_' . $i . '_' . $n]) : ''?>" />
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','URL');?></label>
                                <input type="text" maxlength="250" class="form-control" name="web_button_web_url_<?php echo $i?>_<?php echo $n?>" value="<?php isset($item->configuration_array['web_button_gen']['web_button_web_url_' . $i . '_' . $n]) ? print htmlspecialchars($item->configuration_array['web_button_gen']['web_button_web_url_' . $i . '_' . $n]) : ''?>" />
                            </div>
                        </div>
                    <?php endfor;?>
                </div>
                <hr>
            </div>
        <?php endfor; ?>
    </div>
</div>

<script>
    function changeTemplate(inst){
        console.log(inst.val());
        $('.button-template').hide();
        $('#button-type-' + inst.val()).show();
    }
    changeTemplate($('#template-selector'));
</script>