<form action="" method="post" ng-non-bindable>

    <h3 class="attr-header">Facebook Messenger Options</h3>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="new_chat" <?php isset($fb_options['new_chat']) && ($fb_options['new_chat'] == true) ? print 'checked="checked"' : ''?> /> Create a new chat if chat was closed</label><br/>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="block_bot" <?php isset($fb_options['block_bot']) && ($fb_options['block_bot'] == true) ? print 'checked="checked"' : ''?> /> Block bot, chat will be never forwarded to bot</label><br/>
    </div>

    <div class="form-group">
        <label>Priority</label>
        <input class="form-control form-control-sm" type="text" name="priority" value="<?php (isset($fb_options['priority'])) ? print htmlspecialchars($fb_options['priority']) : print 0?>" />
        <p><i><small>Set what priority chat's should get. The lower the lower priority. Settings priority to (-1) will make them appear at the bottom of pending chats list.</small></i></p>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="exclude_workflow" <?php isset($fb_options['exclude_workflow']) && ($fb_options['exclude_workflow'] == true) ? print 'checked="checked"' : ''?> /> Exclude chats from auto assign timeout workflow.</label>
        <p><i><small>Chat's won't participate in "Chats waiting in pending queue more than n seconds should be auto-assigned first."</small></i></p>
    </div>

    <div class="form-group">
        <label><input type="checkbox" value="on" name="chat_attr" <?php isset($fb_options['chat_attr']) && ($fb_options['chat_attr'] == true) ? print 'checked="checked"' : ''?> />Do not store facebook user name and surname as chat nick</label>
        <p><i><small>Facebook user name and surname will be shown as chat attributes.</small></i></p>
    </div>

    <h3 class="attr-header">WhatsApp options</h3>

    <div class="form-group">
        <label>Permanent WhatsApp access token</label>
        <input class="form-control form-control-sm" type="text" name="whatsapp_access_token" value="<?php (isset($fb_options['whatsapp_access_token'])) ? print htmlspecialchars($fb_options['whatsapp_access_token']) : print ''?>" />
        <p>
            <i><small><a href="https://www.facebook.com/business/help/503306463479099">https://www.facebook.com/business/help/503306463479099</a></small></i><br>
            <i><small><a href="https://developers.facebook.com/docs/whatsapp/business-management-api/get-started#1--acquire-an-">https://developers.facebook.com/docs/whatsapp/business-management-api/get-started#1--acquire-an-</a></small></i><br>
        </p>
    </div>
    <div class="form-group">
        <label>WhatsApp Business Account ID</label>
        <input class="form-control form-control-sm" type="text" name="whatsapp_business_account_id" value="<?php (isset($fb_options['whatsapp_business_account_id'])) ? print htmlspecialchars($fb_options['whatsapp_business_account_id']) : print ''?>" />
    </div>

    <div class="form-group">
        <label>Accept only those phone number ID's. Separated by comma. Phone numbers list has to include from all business accounts.</label>
        <input class="form-control form-control-sm" placeholder="E.g 233359883100000,233359883111100" type="text" name="whatsapp_business_account_phone_number" value="<?php (isset($fb_options['whatsapp_business_account_phone_number'])) ? print htmlspecialchars($fb_options['whatsapp_business_account_phone_number']) : print ''?>" />
    </div>

    <p>Once it's activate we will create necessary dependencies. Rest API, Bot and Incoming web hook configuration.</p>

    <p>You will see what information you have to put</p>

    <h6>Incoming webhook</h6>
    <?php if ($incomingWebhook = erLhcoreClassModelChatIncomingWebhook::findOne(['filter' => ['name' => 'FacebookWhatsApp']])) : ?>
        <p class="text-success">Exists</p>
        <div class="form-group">
            <label>Callback URL for Facebook WhatsApp integration</label>
            <input readonly type="text" class="form-control form-control-sm" value="https://<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::baseurl('webhooks/incoming')?>/<?php echo htmlspecialchars($incomingWebhook->identifier)?>">
        </div>
    <?php else : ?>
        <p class="text-danger">Missing</p>
    <?php endif; ?>

    <h6>Rest API configuration</h6>
    <?php if (erLhcoreClassModelGenericBotRestAPI::getCount(['filter' => ['name' => 'FacebookWhatsApp']]) > 0) : ?>
        <p class="text-success">Exists</p>
    <?php else : ?>
        <p class="text-danger">Missing</p>
    <?php endif; ?>

    <h6>Bot Configuration</h6>
    <?php if ($bot = erLhcoreClassModelGenericBotBot::findOne(['filter' => ['name' => 'FacebookWhatsApp']])) : ?>
        <p class="text-success">Exists</p>
    <?php else : ?>
        <p class="text-danger">Missing</p>
    <?php endif; ?>

    <h6>Event listeners</h6>
    <?php if ($bot && erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.desktop_client_admin_msg', 'bot_id' => $bot->id]]])) : ?>
        <p class="text-success">chat.desktop_client_admin_msg</p>
    <?php else : ?>
        <p class="text-danger">chat.desktop_client_admin_msg</p>
    <?php endif; ?>

    <?php if ($bot && erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.web_add_msg_admin', 'bot_id' => $bot->id]]])) : ?>
        <p class="text-success">chat.web_add_msg_admin</p>
    <?php else : ?>
        <p class="text-danger">chat.web_add_msg_admin</p>
    <?php endif; ?>

    <?php if ($bot && erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.workflow.canned_message_before_save', 'bot_id' => $bot->id]]])) : ?>
        <p class="text-success">chat.workflow.canned_message_before_save</p>
    <?php else : ?>
        <p class="text-danger">chat.workflow.canned_message_before_save</p>
    <?php endif; ?>

    <div class="form-group">
        <label>WhatsApp Verify Token</label>
        <input class="form-control form-control-sm" type="text" name="whatsapp_verify_token" value="<?php (isset($fb_options['whatsapp_verify_token'])) ? print htmlspecialchars($fb_options['whatsapp_verify_token']) : print ''?>" />
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />&nbsp;
    <input type="submit" class="btn btn-secondary" name="StoreOptionsWhatsApp" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save and Activate WhatsApp configuration'); ?>" />&nbsp;
    <input type="submit" class="btn btn-warning" name="StoreOptionsWhatsAppRemove" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save And Remove WhatsApp configuration'); ?>" />

</form>
