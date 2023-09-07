<?php

namespace LiveHelperChatExtension\fbmessenger\providers;
#[\AllowDynamicProperties]
class FBMessengerWhatsAppLiveHelperChatActivator {

    public static function remove()
    {
        if ($incomingWebhook = \erLhcoreClassModelChatIncomingWebhook::findOne(['filter' => ['name' => 'FacebookWhatsApp']])) {
            $incomingWebhook->removeThis();
        }

        if ($restAPI = \erLhcoreClassModelGenericBotRestAPI::findOne(['filter' => ['name' => 'FacebookWhatsApp']])) {
            $restAPI->removeThis();
        }

        if ($botPrevious = \erLhcoreClassModelGenericBotBot::findOne(['filter' => ['name' => 'FacebookWhatsApp']])) {
            $botPrevious->removeThis();

            if ($event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.desktop_client_admin_msg', 'bot_id' => $botPrevious->id]]])) {
                $event->removeThis();
            }

            if ($event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.workflow.canned_message_before_save', 'bot_id' => $botPrevious->id]]])) {
                $event->removeThis();
            }

            if ($event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.web_add_msg_admin', 'bot_id' => $botPrevious->id]]])) {
                $event->removeThis();
            }

            if ($event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.before_auto_responder_msg_saved', 'bot_id' => $botPrevious->id]]])) {
                $event->removeThis();
            }
        }
    }

    public static function installOrUpdate()
    {
        // GoogleBusinessMessage
        $incomingWebhook = \erLhcoreClassModelChatIncomingWebhook::findOne(['filter' => ['name' => 'FacebookWhatsApp']]);

        $fbOptions = \erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
        $data = (array)$fbOptions->data;

        $incomingWebhookContent = str_replace('{whatsapp_access_token}', $data['whatsapp_access_token'], file_get_contents('extension/fbmessenger/doc/whatsapp/incoming-webhook.json'));
        $content = json_decode($incomingWebhookContent,true);

        if (!$incomingWebhook) {
            $incomingWebhook = new \erLhcoreClassModelChatIncomingWebhook();
            $incomingWebhook->setState($content);
            $incomingWebhook->dep_id = 1;
            $incomingWebhook->name = 'FacebookWhatsApp';
            $incomingWebhook->identifier = \erLhcoreClassModelForgotPassword::randomPassword(20);
        } else {
            $dep_id = $incomingWebhook->dep_id;
            $identifier = $incomingWebhook->identifier;
            $incomingWebhook->setState($content);
            $incomingWebhook->dep_id = $dep_id;
            $incomingWebhook->identifier = $identifier;
            $incomingWebhook->name = 'FacebookWhatsApp';
        }
        $incomingWebhook->saveThis();

        // RestAPI
        $restAPI = \erLhcoreClassModelGenericBotRestAPI::findOne(['filter' => ['name' => 'FacebookWhatsApp']]);
        $content = json_decode(file_get_contents('extension/fbmessenger/doc/whatsapp/rest-api.json'),true);

        if (!$restAPI) {
            $restAPI = new \erLhcoreClassModelGenericBotRestAPI();
        }

        $restAPI->setState($content);
        $restAPI->name = 'FacebookWhatsApp';
        $restAPI->saveThis();

        if ($botPrevious = \erLhcoreClassModelGenericBotBot::findOne(['filter' => ['name' => 'FacebookWhatsApp']])) {
            $botPrevious->removeThis();
        }

        $botData = \erLhcoreClassGenericBotValidator::importBot(json_decode(file_get_contents('extension/fbmessenger/doc/whatsapp/bot-data.json'),true));
        $botData['bot']->name = 'FacebookWhatsApp';
        $botData['bot']->updateThis(['update' => ['name']]);

        $trigger = $botData['triggers'][0];
        $actions = $trigger->actions_front;
        $actions[0]['content']['rest_api'] = $restAPI->id;
        $trigger->actions_front = $actions;
        $trigger->actions = json_encode($actions);
        $trigger->updateThis(['update' => ['actions']]);

        if ($botPrevious && $event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.desktop_client_admin_msg', 'bot_id' => $botPrevious->id]]])) {
            $event->removeThis();
        }
        $event = new \erLhcoreClassModelChatWebhook();
        $event->setState(json_decode(file_get_contents('extension/fbmessenger/doc/whatsapp/chat.desktop_client_admin_msg.json'),true));
        $event->bot_id = $botData['bot']->id;
        $event->trigger_id = $trigger->id;
        $event->saveThis();

        if ($botPrevious && $event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.workflow.canned_message_before_save', 'bot_id' => $botPrevious->id]]])) {
            $event->removeThis();
        }
        $event = new \erLhcoreClassModelChatWebhook();
        $event->setState(json_decode(file_get_contents('extension/fbmessenger/doc/whatsapp/chat.workflow.canned_message_before_save.json'),true));
        $event->bot_id = $botData['bot']->id;
        $event->trigger_id = $trigger->id;
        $event->saveThis();

        if ($botPrevious && $event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.web_add_msg_admin', 'bot_id' => $botPrevious->id]]])) {
            $event->removeThis();
        }
        $event = new \erLhcoreClassModelChatWebhook();
        $event->setState(json_decode(file_get_contents('extension/fbmessenger/doc/whatsapp/chat.web_add_msg_admin.json'),true));
        $event->bot_id = $botData['bot']->id;
        $event->trigger_id = $trigger->id;
        $event->saveThis();
        
        if ($botPrevious && $event = \erLhcoreClassModelChatWebhook::findOne(['filter' => ['event' => ['chat.before_auto_responder_msg_saved', 'bot_id' => $botPrevious->id]]])) {
            $event->removeThis();
        }
        $event = new \erLhcoreClassModelChatWebhook();
        $event->setState(json_decode(file_get_contents('extension/fbmessenger/doc/whatsapp/chat.before_auto_responder_msg_saved.json'),true));
        $event->bot_id = $botData['bot']->id;
        $event->trigger_id = $trigger->id;
        $event->saveThis();
    }
}

?>