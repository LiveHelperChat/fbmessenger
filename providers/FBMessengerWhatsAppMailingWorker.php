<?php

namespace LiveHelperChatExtension\fbmessenger\providers;
#[\AllowDynamicProperties]
class FBMessengerWhatsAppMailingWorker {

    public function perform()
    {
        $cfg = \erConfigClassLhConfig::getInstance();
        $worker = $cfg->getSetting( 'webhooks', 'worker' );

        $db = \ezcDbInstance::get();
        $db->reconnect(); // Because it timeouts automatically, this calls to reconnect to database, this is implemented in 2.52v

        $db->beginTransaction();
        $campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetchAndLock($this->args['campaign_id']);

        // Campaign was terminated in the middle of process
        if ($campaign->enabled == 0) {
            return;
        }

        $campaign->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_IN_PROGRESS;
        $campaign->updateThis(['update' => ['status']]);
        $db->commit();

        $instance = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

        if ($campaign->business_account_id > 0) {
            $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($campaign->business_account_id);
            $instance->setAccessToken($account->access_token);
            $instance->setBusinessAccountID($account->business_account_id);
        }

        $templates = $instance->getTemplates();
        $phones = $instance->getPhones();

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT `id` FROM lhc_fbmessengerwhatsapp_campaign_recipient WHERE campaign_id = :campaign_id AND status = :status LIMIT :limit FOR UPDATE ');
            $stmt->bindValue(':limit',20,\PDO::PARAM_INT);
            $stmt->bindValue(':status', \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING,\PDO::PARAM_INT);
            $stmt->bindValue(':campaign_id',$campaign->id,\PDO::PARAM_INT);
            $stmt->execute();
            $recipientsId = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            // Someone is already processing. So we just ignore and retry later
            return;
        }

        if (!empty($recipientsId)) {
            // Delete indexed chat's records
            $stmt = $db->prepare('UPDATE `lhc_fbmessengerwhatsapp_campaign_recipient` SET status = :status WHERE id IN (' . implode(',', $recipientsId) . ')');
            $stmt->bindValue(':status', \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_IN_PROCESS, \PDO::PARAM_INT);
            $stmt->execute();
            $db->commit();

            $recipients = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getList(array('filterin' => array('id' => $recipientsId)));

            if (!empty($recipients)) {

                foreach ($recipients as $recipient) {
                    self::sendMessage($recipient, $campaign, $instance, $templates, $phones, true); // We want only schedule sending, but do not send here
                }

                if ($worker == 'resque' && class_exists('\erLhcoreClassExtensionLhcphpresque') && count($recipients) == 20 && \erLhcoreClassRedis::instance()->llen('resque:queue:lhc_fbwhatsapp_campaign') <= 4) {
                    \erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_fbwhatsapp_campaign', '\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingWorker', array('campaign_id' => $campaign->id));
                }
            }

        } else {

            // Finish previous
            $db->commit();

            $db->beginTransaction();
            $campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetchAndLock($this->args['campaign_id']);
            $campaign->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED;
            $campaign->updateThis(['update' => ['status']]);
            $db->commit();
        }
    }

    public static function sendMessage($recipient, $campaign, $instance, $templates, $phones, $justSchedule = false) {

        $item = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage();

        $item->campaign_recipient_id = $recipient->id;
        $item->phone = $recipient->recipient_phone;
        $item->phone_whatsapp = $recipient->recipient_phone_recipient;
        $item->recipient_id = $recipient->recipient_id;

        $item->private = $campaign->private;
        $item->campaign_id = $campaign->id;
        $item->phone_sender = $campaign->phone_sender;
        $item->phone_sender_id = $campaign->phone_sender_id;
        $item->template = $campaign->template;
        $item->template_id = $campaign->template_id;
        $item->language = $campaign->language;
        $item->user_id = $campaign->user_id;
        $item->dep_id = $campaign->dep_id;
        $item->scheduled_at = $campaign->starts_at;
        $item->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SCHEDULED;
        $item->business_account_id = $campaign->business_account_id;

        // Messages variable
        $messagesVariables = $campaign->message_variables_array;

        foreach ($messagesVariables as $key => $value) {
            $messagesVariables[$key] = \erLhcoreClassGenericBotWorkflow::translateMessage($value, array('args' => ['recipient' => $recipient]));
        }

        $item->message_variables = json_encode($messagesVariables);
        $item->message_variables_array = $messagesVariables;

        if ($justSchedule === false) {
            $instance->sendTemplate($item, $templates, $phones);
        }

        $item->saveThis();

        // Store related message to send message
        $recipient->message_id = $item->id;
        $recipient->opened_at = 0;

        if ($justSchedule === false) {
            $recipient->send_at = time();
        }

        if ($item->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_FAILED) {
            $recipient->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_FAILED;
        } else {
            $recipient->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_SENT;
        }

        $recipient->updateThis(['update' => ['message_id','send_at','status']]);
    }

}

?>