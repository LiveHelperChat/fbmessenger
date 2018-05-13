<div class="col-xs-2">
    <div class="form-group">
        <label>Facebook chat</label>
        <?php $extFb = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger'); ?>
        <input type="checkbox" name="<?php echo $extFb->settings['elastic_search']['search_attr']?>" value="1" <?php if ($input->{$extFb->settings['elastic_search']['search_attr']} == 1) : ?>checked="checked"<?php endif;?> >
    </div>
</div>