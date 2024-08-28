<?php
#[\AllowDynamicProperties]
class erLhcoreClassExtensionFbmessenger {
    
	public function __construct() {
	    
	}
	
	public function run() {
		$this->registerAutoload ();
		
		$dispatcher = erLhcoreClassChatEventDispatcher::getInstance();

		$dispatcher->listen('chat.customcommand', array(
		    $this,
		    'sendTemplate'
		));

		$dispatcher->listen('instance.extensions_structure', array(
		    $this,
		    'checkStructure'
		));
		
		$dispatcher->listen('instance.registered.created', array(
		    $this,
		    'instanceCreated'
		));
		
		$dispatcher->listen('instance.destroyed', array(
		    $this,
		    'instanceDestroyed'
		));

        // Bot related callbacks
        $dispatcher->listen('chat.genericbot_set_bot',array(
                $this, 'allowSetBot')
        );

        // WhatsApp Integration
        $dispatcher->listen('chat.webhook_incoming', array(
            $this,
            'verifyWhatsAppToken'
        ));

        $dispatcher->listen('chat.webhook_incoming', array(
            $this,
            'incommingWebhook'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_started', array(
            $this,
            'incommingChatStarted'
        ));

        $dispatcher->listen('chat.rest_api_before_request', array(
            $this,
            'addWhatsAppToken'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_started', array(
            $this,
            'setWhatsAppToken'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_continue', array(
            $this,
            'setWhatsAppToken'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_before_save', array(
            $this,
            'verifyPhoneBeforeSave'
        ));

        $dispatcher->listen('chat.chat_started', array(
            $this,
            'fetchMessengerUser'
        ));
	}

    public function verifyPhoneBeforeSave($params)
    {
        if (is_object($params['chat']->iwh) && $params['chat']->iwh->scope == 'facebookwhatsappscope') {
            if (isset($params['chat']->chat_variables_array['iwh_field_2'])) {
                $tOptions = \erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
                $data = (array)$tOptions->data;
                if (isset($data['whatsapp_business_account_phone_number']) && !empty($data['whatsapp_business_account_phone_number'])) {
                    $validPhoneNumbers = explode(',',str_replace(' ','',$data['whatsapp_business_account_phone_number']));
                    if (!in_array($params['chat']->chat_variables_array['iwh_field_2'],$validPhoneNumbers)) {
                        echo json_encode(['error' => true, 'message' => 'Not defined phone number - ' . $params['chat']->chat_variables_array['iwh_field_2']]);
                        exit; // Not supported phone number
                    }
                }
            }
        }
    }

    public function setWhatsAppToken($params)
    {
        if (is_object($params['chat']->iwh) && $params['chat']->iwh->scope == 'facebookwhatsappscope') {
            if (isset($params['chat']->chat_variables_array['iwh_field_2'])) {
                $businessAccount = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::findOne(array('customfilter' => array("JSON_CONTAINS(`phone_number_ids`,'\"" . (int)$params['chat']->chat_variables_array['iwh_field_2'] . "\"','$')" )));
                // Override only if we found separate business account for that phone number
                if (is_object($businessAccount)) {
                    $attributes = $params['webhook']->attributes;
                    $attributes['access_token']= $businessAccount->access_token;
                    $params['webhook']->attributes = $attributes;
                }
            }
        } else if (is_object($params['chat']->iwh) && $params['chat']->iwh->scope == 'facebookmessengerappscope') {
            $pageId = $params['data']['entry'][0]['id'];
            $page = erLhcoreClassModelMyFBPage::findOne(array('page_id' => $pageId));
            if (is_object($page)) {
                $attributes = $params['webhook']->attributes;
                $attributes['access_token']= $page->access_token;
                $params['webhook']->attributes = $attributes;
            }
        }
    }

    public function addWhatsAppToken($params) {
        if (is_object($params['chat']->incoming_chat) && $params['chat']->incoming_chat->incoming->scope == 'facebookwhatsappscope') {
            if (isset($params['chat']->chat_variables_array['iwh_field_2'])) {
                $businessAccount = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::findOne(array('customfilter' => array("JSON_CONTAINS(`phone_number_ids`,'\"" . (int)$params['chat']->chat_variables_array['iwh_field_2'] . "\"','$')" )));
                // Override only if we found separate business account for that phone number
                if (is_object($businessAccount)) {
                    $attributes = $params['chat']->incoming_chat->incoming->attributes;
                    $attributes['access_token']= $businessAccount->access_token;
                    $params['chat']->incoming_chat->incoming->attributes = $attributes;
                }
            }
        } else if (is_object($params['chat']->incoming_chat) && $params['chat']->incoming_chat->incoming->scope == 'facebookmessengerappscope') {
            $pageId = $params['chat']->incoming_chat->chat_external_last;
            $page = erLhcoreClassModelMyFBPage::findOne(array('page_id' => $pageId));
            if (is_object($page)) {
                $attributes = $params['chat']->incoming_chat->incoming->attributes;
                $attributes['access_token']= $page->access_token;
                $params['chat']->incoming_chat->incoming->attributes = $attributes;
            }
        }
    }

    public function sendTemplate($paramsCommand) {
        if ($paramsCommand['command'] == '!fbtemplate') {

            // !fbtemplate {"template_name":"hello_world","template_lang":"en_us","args":{}}
            // !fbtemplate {"template_name":"quick_reply","template_lang":"en","args":{"field_1":"name","field_header_1":"header"}}

            $paramsTemplate = json_decode($paramsCommand['argument'],true);

            $params = $paramsCommand['params'];

            $item = new \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage();

            $instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

            $businessAccount = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::findOne(array('customfilter' => array("JSON_CONTAINS(`phone_number_ids`,'\"" . (int)$params['chat']->chat_variables_array['iwh_field_2'] . "\"','$')" )));

            // Override only if we found separate business account for that phone number
            if (is_object($businessAccount)) {
                $instance->setBusinessAccountID($businessAccount->business_account_id);
                $instance->setAccessToken($businessAccount->access_token);
            }

            // Templates are required for images to be sent
            $templates = $instance->getTemplates();

            $item->template = $paramsTemplate['template_name'];
            $item->language = $paramsTemplate['template_lang'];
            $item->phone_sender_id = $params['chat']->chat_variables_array['iwh_field_2'];
            $item->message_variables_array = $paramsTemplate['args'];
            $item->phone_whatsapp = $params['chat']->incoming_chat->chat_external_first;

            $instance->sendTemplate($item, $templates, [], ['do_not_save' => true]);

            return array(
                'status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW,
                'processed' => true,
                'raw_message' => '!fbtemplate',
                'process_status' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatcommand', 'Template was send!'). ($this->settings['enable_debug'] == true ? ' '.$item->send_status_raw : '')
            );
        }
    }

    // WhatsApp Verify Token override call
    public function verifyWhatsAppToken($params)
    {
        $tOptions = \erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
        $data = (array)$tOptions->data;
        if (
            isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $data['whatsapp_verify_token']
        ) {
            if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {
                echo $_GET['hub_challenge'];
                exit;
            }
        }
    }

    /*
     * Store Initial Message within chat
     * */
    public function incommingChatStarted($params)
    {
        if (
            isset($params['data']['object']) &&
            isset($params['data']['entry']) &&
            $params['data']['object'] == 'whatsapp_business_account'
        ) {
            $initMessageFound = false;
            foreach ($params['data']['entry'] as $entryItem) {
                foreach ($entryItem['changes'] as $changeItem) {
                    if (isset($changeItem['value']['messages'])) {
                        foreach ($changeItem['value']['messages'] as $messageItem) {
                            $fbWhatsAppMessage = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::findOne([
                                'sort' => '`id` DESC',
                                'filter' => [
                                    'phone' => $messageItem['from'],
                                    'phone_sender_id' => $changeItem['value']['metadata']["phone_number_id"]
                                ]
                            ]);
                            // We insert last message only if it does not have chat assigned already
                            // This is change since multiple WhatsApp accounts support was added.
                            if (is_object($fbWhatsAppMessage) && $fbWhatsAppMessage->dep_id > 0 && $fbWhatsAppMessage->chat_id == 0) {
                                // Chat
                                $params['chat']->dep_id = $fbWhatsAppMessage->dep_id;
                                $params['chat']->updateThis(['update' => ['dep_id']]);

                                // Save template message first before saving initial response in the lhc core
                                $msg = new erLhcoreClassModelmsg();
                                $msg->msg = $fbWhatsAppMessage->message;
                                $msg->chat_id = $params['chat']->id;
                                $msg->user_id = $fbWhatsAppMessage->user_id;
                                $msg->time = $fbWhatsAppMessage->created_at;
                                erLhcoreClassChat::getSession()->save($msg);

                                // Update message bird
                                $fbWhatsAppMessage->chat_id = $params['chat']->id;
                                $fbWhatsAppMessage->updateThis(['update' => ['chat_id']]);
                                $initMessageFound = true;

                                // Update campaign related records
                                if ($fbWhatsAppMessage->campaign_recipient_id > 0) {
                                    $campaignRecipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch($fbWhatsAppMessage->campaign_recipient_id);
                                    if (is_object($campaignRecipient)) {
                                        $campaignRecipient->conversation_id = $fbWhatsAppMessage->chat_id;
                                        $campaignRecipient->updateThis(['update' => ['conversation_id']]);
                                    }
                                }

                                // Update contact main attributes
                                if ($fbWhatsAppMessage->recipient_id > 0) {
                                    $contact = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($fbWhatsAppMessage->recipient_id);
                                    if (is_object($contact)) {
                                        $contact->chat_id = $fbWhatsAppMessage->chat_id;
                                        $contact->updateThis(['update' => ['chat_id']]);
                                    }
                                }
                            }
                        }

                        if ($initMessageFound == false) {
                            $businessAccount = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::findOne(array('customfilter' => array("JSON_CONTAINS(`phone_number_ids`,'\"" . (int)$changeItem['value']['metadata']["phone_number_id"] . "\"','$')" )));
                            // Override only if we found separate business account for that phone number
                            if (is_object($businessAccount)) {
                                $params['chat']->dep_id = isset($businessAccount->phone_number_deps_array[(string)$changeItem['value']['metadata']["phone_number_id"]]) ? $businessAccount->phone_number_deps_array[(string)$changeItem['value']['metadata']["phone_number_id"]] : $businessAccount->dep_id;
                            }
                        }
                    }
                }
            }
        } elseif (isset($params['data']['object']) && $params['data']['object'] == 'page' && $params['webhook']->scope == 'facebookmessengerappscope' && isset($params['data']['entry'][0]['id'])) {
            $myFbPage = erLhcoreClassModelMyFBPage::findOne(['filter' => ['page_id' => $params['data']['entry'][0]['id']]]);
            if (is_object($myFbPage) && $myFbPage->dep_id > 0) {
                $params['chat']->dep_id = $myFbPage->dep_id;
                $params['chat']->updateThis(['update' => ['dep_id']]);
            }
        }
    }

    public function fetchMessengerUser($params)
    {
        if (is_object($params['chat']->iwh) && $params['chat']->iwh->scope == 'facebookmessengerappscope') {
            $chatAttribute = $params['webhook']->attributes;
            try {
                $senderId = $params['chat']->incoming_chat->chat_external_first;
                $messenger = Tgallice\FBMessenger\Messenger::create($chatAttribute['access_token']);
                $profile = $messenger->getUserProfile($senderId);
                $lead = erLhcoreClassModelFBLead::findOne(array('filter' => array('user_id' => $senderId)));

                if (!($lead instanceof erLhcoreClassModelFBLead)) {
                    $lead = new erLhcoreClassModelFBLead();
                    $lead->user_id = $senderId;
                    $lead->first_name = $profile->getFirstName();
                    $lead->last_name = $profile->getLastName();
                    $lead->profile_pic = $profile->getProfilePic();
                    $lead->locale = $profile->getLocale();
                    $lead->timezone = $profile->getTimezone();
                    $lead->gender = $profile->getGender();
                    $lead->is_payment_enabled = 0;
                    $lead->ctime = time();
                    $lead->page_id = $params['chat']->incoming_chat->chat_external_last;
                    $lead->type = 1;
                    $lead->dep_id = 0;
                    $lead->saveThis();
                } elseif ($lead->blocked == 1) {
                    $lead->blocked = 0;
                    $lead->saveThis();
                }

               $params['chat']->nick = trim($profile->getFirstName() . ' ' . $profile->getLastName());
               $params['chat']->updateThis(['update' => ['nick']]);

            } catch (Exception $e) {
                erLhcoreClassLog::write($e->getMessage());
            }
        }
    }

    public function incommingWebhook($params)
    {
        if (
            isset($params['data']['object']) &&
            isset($params['data']['entry']) &&
            $params['data']['object'] == 'whatsapp_business_account'
        ) {
            $skippedMessage = false;
            foreach ($params['data']['entry'] as $entryItem) {
                foreach ($entryItem['changes'] as $changeItem) {
                    if (isset($changeItem['value']['statuses'])) {
                        foreach ($changeItem['value']['statuses'] as $statusItem) {
                            $fbWhatsAppMessage = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::findOne(['filter' => ['fb_msg_id' => $statusItem['id']]]);
                            if ($fbWhatsAppMessage instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage) {

                                $statusMap = [
                                    'pending' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING,
                                    'sent' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_SENT,
                                    'delivered' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_DELIVERED,
                                    'read' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ,
                                    'rejected' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_REJECTED,
                                ];

                                if (isset($statusItem['conversation']['id'])) {
                                    $fbWhatsAppMessage->conversation_id = $statusItem['conversation']['id'];
                                }

                                if ($fbWhatsAppMessage->status != \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ) {
                                    $fbWhatsAppMessage->status = $statusMap[$statusItem['status']];
                                }

                                if ($fbWhatsAppMessage->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_REJECTED) {
                                    $fbWhatsAppMessage->send_status_raw = $fbWhatsAppMessage->send_status_raw . json_encode($params['data']);
                                }

                                if ($fbWhatsAppMessage->campaign_recipient_id > 0) {
                                    $campaignRecipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch($fbWhatsAppMessage->campaign_recipient_id);
                                    if (is_object($campaignRecipient)) {
                                        if ($campaignRecipient->status != \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ) {
                                            $campaignRecipient->status = $statusMap[$statusItem['status']];

                                            if ($campaignRecipient->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ) {
                                                $campaignRecipient->opened_at = time();
                                            }

                                            $campaignRecipient->updateThis(['update' => ['status', 'opened_at']]);
                                        }
                                    }
                                }

                                $chatId = 0;

                                // Insert message as a normal message to the last chat customer had
                                // In case there is chosen reopen old chat
                                // Which by the case is the default option of the extension
                                if (in_array($fbWhatsAppMessage->status,[
                                    \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ
                                ]) && $fbWhatsAppMessage->chat_id == 0) {
                                    $presentConversation = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::findOne([
                                        'filter' => [
                                            'phone_sender_id' => $fbWhatsAppMessage->phone_sender_id,
                                            'phone' => $fbWhatsAppMessage->phone
                                        ],
                                        'filternot' => [
                                            'chat_id' => 0
                                        ],
                                        'sort' => '`id` DESC'
                                    ]);

                                    if (is_object($presentConversation)) {
                                        $chat = erLhcoreClassModelChat::fetch($presentConversation->chat_id);
                                        if (is_object($chat)) {
                                             // Save template message first before saving initial response in the lhc core
                                            $msg = new erLhcoreClassModelmsg();
                                            $msg->msg = $fbWhatsAppMessage->message;
                                            $chatId = $msg->chat_id = $chat->id;
                                            $msg->user_id = $fbWhatsAppMessage->user_id;
                                            $msg->time = $fbWhatsAppMessage->created_at;
                                            erLhcoreClassChat::getSession()->save($msg);

                                            $chat->last_msg_id = $msg->id;
                                            $chat->updateThis(['update' => ['last_msg_id']]);
                                        }
                                    } else { // Try to find any on-going chat
                                        $chatIdExternal = $fbWhatsAppMessage->phone;

                                        $conditions = $params['webhook']->conditions_array;

                                        if (isset($conditions['chat_id_preg_rule']) && $conditions['chat_id_preg_rule'] != '') {
                                            $chatIdExternal = preg_replace($conditions['chat_id_preg_rule'], $conditions['chat_id_preg_value'], $chatIdExternal);
                                        }

                                        $incomingChat = erLhcoreClassModelChatIncoming::findOne(array('filter' => array('chat_external_id' => $chatIdExternal . '__' . $fbWhatsAppMessage->phone_sender_id)));

                                        if ($incomingChat instanceof erLhcoreClassModelChatIncoming && is_object($incomingChat->chat)) {
                                            $msg = new erLhcoreClassModelmsg();
                                            $msg->msg = $fbWhatsAppMessage->message;
                                            $chatId = $msg->chat_id = $incomingChat->chat->id;
                                            $msg->user_id = $fbWhatsAppMessage->user_id;
                                            $msg->time = $fbWhatsAppMessage->created_at;
                                            erLhcoreClassChat::getSession()->save($msg);

                                            $incomingChat->chat->last_msg_id = $msg->id;
                                            $incomingChat->chat->updateThis(['update' => ['last_msg_id']]);
                                        }
                                    }
                                }

                                $fbWhatsAppMessage->saveThis();

                                // Update contact main attributes
                                if ($fbWhatsAppMessage->recipient_id > 0) {
                                    $contact = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($fbWhatsAppMessage->recipient_id);

                                    if (is_object($contact)) {

                                        $contact->chat_id = $fbWhatsAppMessage->chat_id > 0 ? $fbWhatsAppMessage->chat_id : $chatId;

                                        if ($fbWhatsAppMessage->status == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_REJECTED) {
                                            $contact->delivery_status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED;
                                        }

                                       if (in_array((int)$fbWhatsAppMessage->status,[
                                                \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_READ,
                                                \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_DELIVERED
                                            ]) &&
                                            in_array((int)$contact->delivery_status,[
                                                \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_FAILED,
                                                \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_UNKNOWN
                                            ])
                                        ) {
                                            $contact->delivery_status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::DELIVERY_STATUS_ACTIVE;
                                        }

                                        $contact->updateThis(['update' => ['delivery_status', 'chat_id']]);
                                    }
                                }

                                if (isset($campaignRecipient) && is_object($campaignRecipient) && ($fbWhatsAppMessage->chat_id > 0 ||  $chatId > 0)) {
                                    $campaignRecipient->conversation_id = $fbWhatsAppMessage->chat_id > 0 ? $fbWhatsAppMessage->chat_id : $chatId;
                                    $campaignRecipient->updateThis(['update' => ['conversation_id']]);
                                }

                            } else { // It was normal message delivery status change

                                $conditions = $params['webhook']->conditions_array;

                                $chatIdExternal = $statusItem['recipient_id'];

                                if (isset($conditions['chat_id_preg_rule']) && $conditions['chat_id_preg_rule'] != '') {
                                    $chatIdExternal = preg_replace($conditions['chat_id_preg_rule'], $conditions['chat_id_preg_value'], $chatIdExternal);
                                }

                                $incomingChat = erLhcoreClassModelChatIncoming::findOne(array('filter' => array('chat_external_id' => $chatIdExternal.'__'.$changeItem['value']['metadata']['phone_number_id'])));

                                // Chat was found, now we need to find exact message
                                if ($incomingChat instanceof erLhcoreClassModelChatIncoming && is_object($incomingChat->chat)) {

                                    $statusMap = [
                                        'pending' => erLhcoreClassModelmsg::STATUS_PENDING,
                                        'sent' => erLhcoreClassModelmsg::STATUS_SENT,
                                        'delivered' => erLhcoreClassModelmsg::STATUS_DELIVERED,
                                        'read' =>  erLhcoreClassModelmsg::STATUS_READ,
                                        'rejected' =>  erLhcoreClassModelmsg::STATUS_REJECTED
                                    ];

                                    $msg = erLhcoreClassModelmsg::findOne(['filter' => ['chat_id' => $incomingChat->chat->id],'customfilter' => ['`meta_msg` != \'\' AND JSON_EXTRACT(meta_msg,\'$.iwh_msg_id\') = ' . ezcDbInstance::get()->quote($statusItem['id'])]]);

                                    if (is_object($msg) && $msg->del_st != erLhcoreClassModelmsg::STATUS_READ) {

                                        $msg->del_st = max($statusMap[$statusItem['status']],$msg->del_st);
                                        $msg->updateThis(['update' => ['del_st']]);

                                        // Refresh message delivery status for op
                                        $chat = $incomingChat->chat;
                                        $chat->operation_admin .= "lhinst.updateMessageRowAdmin({$msg->chat_id},{$msg->id});";
                                        if ($msg->del_st == erLhcoreClassModelmsg::STATUS_READ) {
                                            $chat->has_unread_op_messages = 0;
                                        }
                                        $chat->updateThis(['update' => ['operation_admin','has_unread_op_messages']]);

                                        // NodeJS to update message delivery status
                                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', array('msg' => & $msg, 'chat' => & $chat));
                                    }

                                }
                            }
                        }
                    } else {
                        $skippedMessage = true;
                    }
                }
            }

            // There was only our processed messages in the callback
            // No need to process anything else
            if ($skippedMessage === false) {
                exit;
            }
        } elseif (
            // This is echo message from our own API Call
            $params['webhook']->scope == 'facebookmessengerappscope' &&
            isset($params['data']['entry'][0]['messaging'][0]['message']['is_echo']) &&
            isset($params['data']['entry'][0]['messaging'][0]['message']['app_id']) &&
            $params['data']['entry'][0]['messaging'][0]['message']['app_id'] == $this->settings['app_settings']['app_id']
        ) {
           exit;
        }
    }

    public static function allowSetBot($params)
    {
        $chat = $params['chat'];

        $variablesArray = $chat->chat_variables_array;

        if (isset($variablesArray['fb_chat']) && is_numeric($variablesArray['fb_chat'])) {

            $tOptions = \erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
            $data = (array)$tOptions->data;

            if (isset($data['block_bot']) && $data['block_bot'] == 1) {
                return array('status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW);
            }
        }
    }



	/**
	 * Checks automated hosting structure
	 *
	 * This part is executed once in manager is run this cronjob.
	 * php cron.php -s site_admin -e instance -c cron/extensions_update
	 *
	 * */
	public function checkStructure()
	{
	    erLhcoreClassUpdate::doTablesUpdate(json_decode(file_get_contents('extension/fbmessenger/doc/structure.json'), true));
	}

	/**
	 * Used only in automated hosting enviroment
	 */
	public function instanceDestroyed($params)
	{
	    // Set subdomain manual, so we avoid calling in cronjob
	    $this->instanceManual = $params['instance'];
	}
	
	/**
	 * Used only in automated hosting enviroment
	 */
	public function instanceCreated($params)
	{
	    try {
	        // Instance created trigger
	        $this->instanceManual = $params['instance'];
	
	        // Just do table updates
	        erLhcoreClassUpdate::doTablesUpdate(json_decode(file_get_contents('extension/fbmessenger/doc/structure.json'), true));
	
	    } catch (Exception $e) {
	        erLhcoreClassLog::write(print_r($e, true));
	    }
	}

	public function registerAutoload() {
		spl_autoload_register ( array (
				$this,
				'autoload'
		), true, false );
	}
	
	public function autoload($className) {
		$classesArray = array (
				'erLhcoreClassModelFBPage'  => 'extension/fbmessenger/classes/erlhcoreclassmodelfbpage.php',
				'erLhcoreClassModelFBBBCode'=> 'extension/fbmessenger/classes/erlhcoreclassmodelfbbbcode.php',
				'erLhcoreClassFBValidator'                          => 'extension/fbmessenger/classes/erlhcoreclassfbvalidator.php',
				'erLhcoreClassModelFBMessengerUser'                 => 'extension/fbmessenger/classes/erlhcoreclassmodelfbuser.php',
				'erLhcoreClassModelMyFBPage'                        => 'extension/fbmessenger/classes/erlhcoreclassmodelmyfbpage.php',
				'erLhcoreClassModelFBLead'                          => 'extension/fbmessenger/classes/erlhcoreclassmodelfblead.php',
				'erLhcoreClassModelFBNotificationSchedule'          => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationschedule.php',
				'erLhcoreClassModelFBNotificationStatus'            => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationstatus.php',
				'erLhcoreClassModelFBNotificationScheduleCampaign'  => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationschedulecampaign.php',
				'erLhcoreClassModelFBNotificationScheduleItem'      => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationscheduleitem.php'
		);

		if (key_exists ( $className, $classesArray )) {
			include_once $classesArray [$className];
		}
	}
	
	public static function getSession() {
		if (! isset ( self::$persistentSession )) {
			self::$persistentSession = new ezcPersistentSession ( ezcDbInstance::get (), new ezcPersistentCodeManager ( './extension/fbmessenger/pos' ) );
		}
		return self::$persistentSession;
	}
	
	public function setPage($page) {
	    $this->fbpage = $page;
	}
	
	public function getPage() {
	    return $this->fbpage;
	}
	
	public function __get($var) {
		switch ($var) {
			case 'is_active' :
				return true;
				;
				break;
			
			case 'settings' :
				$this->settings = include ('extension/fbmessenger/settings/settings.ini.php');				
				return $this->settings;
				break;
			
			default :
				;
				break;
		}
	}
	
	private static $persistentSession;
	
	private $fbpage = null;
	
	private $configData = false;
	
	private $instanceManual = false;
}


