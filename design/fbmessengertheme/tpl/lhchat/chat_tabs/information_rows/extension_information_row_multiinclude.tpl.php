<?php if ($buttonData['item'] == 'fb_chat') : ?>

    <?php if (isset($chat->chat_variables_array['fb_chat']) && $chat->chat_variables_array['fb_chat'] == true) : ?>
    <tr ng-non-bindable>
        <td>
            <img width="14" src="<?php echo erLhcoreClassDesign::design('images/F_icon.svg')?>" title="Facebook chat" />&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','FB Chat')?>
        </td>
        <td>
            <b>YES,</b> <?php echo htmlspecialchars($chat->chat_variables_array['fb_gender'])?>
        </td>
    </tr>
    <?php
    $fbLead = erLhcoreClassModelFBLead::findOne(array('filter' => array('user_id' => $chat->chat_variables_array['fb_user_id'])));
    if ($fbLead instanceof erLhcoreClassModelFBLead) : $fbNotification = erLhcoreClassModelFBNotificationScheduleItem::findOne(array('filter' => array('lead_id' => $fbLead->id,'status' => 1)));
    if ($fbNotification instanceof erLhcoreClassModelFBNotificationScheduleItem) : $fbSchedule = erLhcoreClassModelFBNotificationSchedule::fetch($fbNotification->schedule_id);
    if ($fbSchedule instanceof erLhcoreClassModelFBNotificationSchedule) : ?>
    <tr ng-non-bindable>
        <td colspan="2">
            <h5><strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Last marketing message')?></strong></h5>
            <?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($fbSchedule->message));?>
        </td>
    </tr>
    <?php endif; endif; endif; endif; ?>

<?php endif; ?>
