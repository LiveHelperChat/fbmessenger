<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Name');?></label>
    <input type="text" maxlength="250" class="form-control" name="name" value="<?php echo htmlspecialchars($item->name)?>" />
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Start sending at');?> (<?php echo date_default_timezone_get()?>)</label>
            <input type="text" maxlength="250" class="form-control" name="start_at" id="id_start_at"  value="<?php echo htmlspecialchars($item->start_at_day)?>" />
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
            <div class="row">
                <div class="col-md-6">
                    <select name="start_at_hours" class="form-control">
                        <option value="">Select hour</option>
                        <?php for ($i = 0; $i <= 23; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if ($item->start_at_hour === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="col-md-6">
                    <select name="start_at_minutes" class="form-control">
                        <option value="">Select minute</option>
                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if ($item->start_at_minute === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <label><input type="checkbox" name="processed" value="on" <?php ($item->status == 1) ? print ' checked="checked" ' : ''?> /> Processed (after sending was activated you unchecked this to enable compaign sending again)</label>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Interval between send in seconds');?></label>
            <input type="text" maxlength="250" class="form-control" name="interval" value="<?php echo htmlspecialchars($item->interval)?>" />
        </div>
     </div>
     <div class="col-md-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Batch size');?></label>
            <input type="text" maxlength="250" class="form-control" name="amount" value="<?php echo htmlspecialchars($item->amount)?>" />
        </div>
     </div>
</div>

<hr>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Gender');?></label>
            <select name="filter_gender" class="form-control">
                <option value="">Any</option>
                <option value="male" <?php if (isset($item->filter_array['gender']) && $item->filter_array['gender'] == 'male') : ?>selected="selected"<?php endif;?> >Male</option>
                <option value="female" <?php if (isset($item->filter_array['gender']) && $item->filter_array['gender'] == 'female') : ?>selected="selected"<?php endif;?> >Female</option>
            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Department');?></label>
            <?php echo erLhcoreClassRenderHelper::renderCombobox(array(
                'input_name'     => 'dep_id',
                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select department'),
                'selected_id'    => ((isset($item->filter_array['dep_id'])) ? $item->filter_array['dep_id'] : 0),
                'css_class'      => 'form-control',
                'list_function'  => 'erLhcoreClassModelDepartament::getList',
                'list_function_params'  => array(),
            )); ?>
        </div>
    </div>
</div>






<ul class="nav nav-pills" role="tablist">
    <li role="presentation" class="nav-item"><a class="active nav-link" href="#message" aria-controls="message" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Message');?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#status" aria-controls="status" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Status');?></a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="message">

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Message');?></label>
                    <textarea name="message" id="message-text" class="form-control"><?php echo htmlspecialchars($item->message)?></textarea>
                </div>
            </div>
            <div class="col-6">
                <div id="status-test-send"></div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Lead ID');?></label>
                    <input type="text" maxlength="250" class="form-control" id="id_fb_user_id" name="fb_user_id" value="<?php echo htmlspecialchars(isset($item->test_data['fb_user_id']) ? $item->test_data['fb_user_id'] : '')?>" />
                </div>
                <input type="button" class="btn btn-default" id="id-send-test-message" name="SendTestLead" value="Send test message">
            </div>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane" id="status">

        <?php if ($item->id > 0) : $notificationsCampaigns = erLhcoreClassModelFBNotificationScheduleCampaign::getList(array('limit' => 5, 'filter' => array('schedule_id' => $item->id))); ?>

        <?php if (count($notificationsCampaigns) > 0) : ?>
        <table class="table-bordered table-striped table-condensed" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Recipients</th>
                <th>Processed</th>
                <th>Errors</th>
            </tr>
        </thead>
        <?php foreach ($notificationsCampaigns as $scheduleItem) : ?>
            <tr>
                <td><?php echo $scheduleItem->id?></td>
                <td><?php echo $scheduleItem->ctime_front?></td>
                <td>
                    <?php if ($scheduleItem->status == erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_PENDING) : ?>
                        Pending
                    <?php elseif ($scheduleItem->status == erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTING) : ?>
                        Collecting recipients
                    <?php elseif ($scheduleItem->status == erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTED) : ?>
                        Collected
                    <?php elseif ($scheduleItem->status == erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_SENDING) : ?>
                        Sending
                    <?php elseif ($scheduleItem->status == erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_SEND) : ?>
                        Send
                    <?php endif ?>
                </td>
                <td>
                    <?php echo erLhcoreClassModelFBNotificationScheduleItem::getCount(array('filter' => array('campaign_id' => $scheduleItem->id)))?>
                </td>
                <td>
                    <?php echo erLhcoreClassModelFBNotificationScheduleItem::getCount(array('filter' => array('status' => erLhcoreClassModelFBNotificationScheduleItem::STATUS_PROCESSED, 'campaign_id' => $scheduleItem->id)))?>
                </td>
                <td>
                    <?php echo erLhcoreClassModelFBNotificationScheduleItem::getCount(array('filter' => array('status' => erLhcoreClassModelFBNotificationScheduleItem::STATUS_ERROR, 'campaign_id' => $scheduleItem->id)))?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
        <?php else : ?>
                <p>No campaigns were found.</p>
        <?php endif ?>

        <?php else : ?>
                <p>Please save notification first.</p>
        <?php endif; ?>

        <br/>
    </div>
</div>

<script>
    $(function() {
        $('#id_start_at').fdatepicker({
            format: 'yyyy-mm-dd'
        });

        $('#id-send-test-message').click(function(){
            var inst = $(this);
            inst.val('Sending...');
            $.postJSON(WWW_DIR_JAVASCRIPT + 'fbmessenger/sendtestmessage', {'user_id' : $('#id_fb_user_id').val(), msg:$('#message-text').val()}, function(data) {
                $('#status-test-send').html('<div role="alert" class="alert alert-info">'  + data.msg + '</div>');
                inst.val('Send test message');
            });
        });

    });
</script>