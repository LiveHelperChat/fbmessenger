<?php
#[\AllowDynamicProperties]
class erLhcoreClassExtensionFbmessenger {
    
	public function __construct() {

	}
	
	public function run() {
		$this->registerAutoload ();

		$dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
		
		$dispatcher->listen('chat.web_add_msg_admin', array(
		    $this,
		    'sendMessageToFb'
		));

        $dispatcher->listen('chat.before_auto_responder_msg_saved', array(
            $this,
            'sendMessageToFb'
        ));

        $dispatcher->listen('chat.customcommand', array(
            $this,
            'sendTemplate'
        ));

        $dispatcher->listen('chat.auto_preload', array(
            $this,
            'autoPreload'
        ));

		$dispatcher->listen('chat.desktop_client_admin_msg', array(
		    $this,
		    'sendMessageToFb'
		));
		
		$dispatcher->listen('chat.workflow.canned_message_before_save', array(
		    $this,
		    'sendMessageToFb'
		));
		
		$dispatcher->listen('chat.delete', array(
		    $this,
		    'deleteChat'
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

        $dispatcher->listen('chat.workflow.autoassign', array(
            $this,
            'autoAssignBlock'
        ));

		$dispatcher->listen('chat.syncadmin', array(
		    $this,
		    'syncAdmin'
		));

        $dispatcher->listen('chat.rest_api_before_request', array(
            $this,
            'addVariables'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_before_update_new', array(
            $this,
            'updateWhatsAppDepartment'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_continue', array(
            $this,
            'updateWhatsAppDepartment'
        ));

        $dispatcher->listen('chat.webhook_incoming_chat_started', array(
            $this,
            'updateWhatsAppDepartment'
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

    public function updateWhatsAppDepartment($params)
    {
        if (isset($params['data']['entry'][0]['changes'][0]['value']['metadata']['phone_number_id']) && is_numeric($params['data']['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'])) {
            $phoneNumberId = $params['data']['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
        } elseif (isset($params['echat']) && is_object($params['echat'])) {
            $phoneNumberId =  $params['echat']->chat_external_last;
        } else {
            return;
        }

        $page = erLhcoreClassModelMyFBPage::findOne(['filter' => ['whatsapp_business_phone_number_id' => $phoneNumberId]]);
        if (is_object($page) && $page->dep_id > 0) {
            $params['chat']->dep_id = $page->dep_id;
            if (isset($params['webhook'])) {
                $params['webhook']->configuration = str_replace('{whatsapp_access_token}',$page->access_token, $params['chat']->incoming_chat->incoming->configuration);
                if (isset($params['webhook']->conditions_array['attr']) && is_array($params['webhook']->conditions_array['attr'])) {
                   $attributes = $params['webhook']->conditions_array;
                   foreach ($attributes['attr'] as $key => $value) {
                       if ($value['value'] == '{whatsapp_access_token}') {
                           $attributes['attr'][$key]['value'] = $page->access_token;
                       }
                   }
                   $params['webhook']->conditions_array = $attributes;
                }
            }
        }
    }

    /**
     *
     * If user disabled auto assign for timed out assign wrofklows
     *
     * @param array $params
     */
    public function autoAssignBlock($params) {
        if (isset($params['chat']) && isset($params['params']['auto_assign_timeout']) && $params['params']['auto_assign_timeout'] == true) {
            $chatVariables = $params['chat']->chat_variables_array;

            if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1)
            {
                $fbOptions = erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
                $data = (array)$fbOptions->data;

                if (isset($data['exclude_workflow']) && $data['exclude_workflow'] == true)
                {
                    return array('status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW, 'user_id' => 0); // Do nothing if it was executed
                }
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

    public function addVariables($params)
    {
        if (is_object($params['chat']->incoming_chat) && $params['chat']->incoming_chat->incoming->scope == 'facebookwhatsappscope') {
            $page = erLhcoreClassModelMyFBPage::findOne(['filter' => ['whatsapp_business_phone_number_id' => $params['chat']->incoming_chat->chat_external_last]]);
            if (is_object($page)){
                $params['chat']->incoming_chat->incoming->configuration = str_replace('{whatsapp_access_token}',$page->access_token, $params['chat']->incoming_chat->incoming->configuration);
            }
        }
    }

	/**
	 * Used only in automated hosting enviroment
	 */
	public function instanceDestroyed()
	{
	    // Set subdomain manual, so we avoid calling in cronjob
	    $this->instanceManual = $params['instance'];
	    
	    // Nothing to do at the moment
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
	
	public function syncAdmin($params)
	{		
		if ($params['chat']->status_sub_sub == 3 || $params['response']['msg'] == '' || strpos($params['response']['msg'], '[fbblock-') !== false) {
			$params['response']['ignore'] = true;
		}
	}
	
	public function sendMessageToFb($params)
	{
    	$chatVariables = $params['chat']->chat_variables_array;
    	
    	if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1 && $params['msg']->user_id != -1)
    	{
    	    try {    	        
    	        $chat = erLhcoreClassModelFBChat::findOne(array('filter' => array('chat_id' => $params['chat']->id)));
    	        
    	        $this->setPage($chat->page);

                $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->access_token);

                if (strpos($params['msg']->msg, '!') !== 0) {
                    // Regular message stop, chat
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare("UPDATE bot_leads SET auto_stop = 1 WHERE user_id = :user_id");
                    $stmt->bindValue(':user_id',$chat->user_id,PDO::PARAM_INT);
                    $stmt->execute();
                }

                $messages = self::parseMessageForFB($params['msg']->msg);

                foreach ($messages as $msg) {
                    if ($msg !== null) {
                        $response = $messenger->sendMessage($chat->user_id, $msg);
                    }
                }

    	    } catch (Exception $e) {

                $msgInitial = new erLhcoreClassModelmsg();
                $msgInitial->msg = "Facebook Error: " . $e->getMessage();
                $msgInitial->chat_id = $params['chat']->id;
                $msgInitial->user_id = -1;
                $msgInitial->time = time ();
                $msgInitial->saveThis();

                $params['chat']->last_msg_id = $msgInitial->id;
                $params['chat']->saveThis();

    	        if ($this->settings['enable_debug'] == true) {
    	            erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
    	        }
    	    }
    	}    	
	}

	/**
	 * @desc parses operator messages and replaces images
	 * 
	 * @todo keep order as it was during initial messages
	 * 
	 * @param string $ret
	 * 
	 * @return multitype:\Tgallice\FBMessenger\Model\Message Ambigous <\Tgallice\FBMessenger\Model\Attachment\Image>
	 */
	public static function parseMessageForFB($ret)
	{	    
	    $matches = array();
	    
	    // Allow extensions to preparse send message
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('fbmessenger.before_parse_send', array('msg' => & $ret));
	    
	    preg_match_all('/\[img\](.*?)\[\/img\]/ms', $ret, $matches);
	    
	    // Parse Images
	    $imagesAttatchements = array();

	    foreach ($matches[1] as $key => $img) {
	        $in = trim($img);

	        $url = erLhcoreClassBBCode::esc_url($in);

	        if ( empty($url) )
	            continue;

            $urlEscaped = urldecode(ltrim($url,"/"));

            if (strpos($urlEscaped,'http://') === false && strpos($urlEscaped,'https://') === false) {
                $urlEscaped = 'https://'.$_SERVER['HTTP_HOST'].'/'. $urlEscaped;
            }

	        $image = new Tgallice\FBMessenger\Model\Attachment\Image($urlEscaped);
	        
	        $imagesAttatchements[] = $image;
	        
	        $ret = preg_replace('/'.preg_quote($matches[0][$key], '/').'/', '[split_img]', $ret, 1);
	    }

	    // Parse files attatchements
	    $matches = array();

	    // File block	   	    
	    preg_match_all('#\[file="?(.*?)"?\]#is', $ret, $matches);
	    
	    foreach ($matches[1] as $key => $fileKey) {
	        
	        list($fileID,$hash) = explode('_',$fileKey);
	        try {
	            $file = erLhcoreClassModelChatFile::fetch($fileID);
	        
	            // AWS plugin changes file name, but we always use original name
	            $parts = explode('/', $file->name);
	            end($parts);
	            $name = end($parts);
	            	
	            // Check that user has permission to see the chat. Let say if user purposely types file bbcode
	            if ($hash == md5($name.'_'.$file->chat_id)) {
	                $hash = md5($file->name.'_'.$file->chat_id);
	                
	                $elements = [
                        new Tgallice\FBMessenger\Model\Button\WebUrl(erTranslationClassLhTranslation::getInstance()->getTranslation('file/file','Download'), 'https://devmysql.livehelperchat.com' . erLhcoreClassDesign::baseurl('file/downloadfile')."/{$file->id}/{$hash}" )
                    ];
	                	                
                    $template = new Tgallice\FBMessenger\Model\Attachment\Template\Button(erTranslationClassLhTranslation::getInstance()->getTranslation('file/file','Download').' - '.htmlspecialchars($file->upload_name).' ['.$file->extension.']', $elements);
	                
	                $imagesAttatchements[] = $template;
	                
	                $ret = preg_replace('/'.preg_quote($matches[0][$key], '/').'/', '[split_img]', $ret, 1);
	            }
	            	
	        } catch (Exception $e) {
	           erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
	        }	        
	    }
	    
	    // Allow extensions to parse text message for final return
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('fbmessenger.before_send', array('msg' => & $ret));

        $bbcodes = erLhcoreClassModelFBBBCode::getList();

        foreach ($bbcodes as $bbcode) {
            if (strpos($ret,'[' . $bbcode->bbcode . ']') !== false) {

                if (isset($bbcode->configuration_array['bbcode_button_type']) && $bbcode->configuration_array['bbcode_button_type'] == 'web_button') {

                    $elements = array();

                    for ($i = 1; $i <= 3; $i++) {
                        if (isset($bbcode->configuration_array['web_button']['web_button_web_title_' . $i]) &&
                            isset($bbcode->configuration_array['web_button']['web_button_web_url_' . $i]) &&
                            !empty($bbcode->configuration_array['web_button']['web_button_web_title_' . $i]) &&
                            !empty($bbcode->configuration_array['web_button']['web_button_web_url_' . $i]))
                        {
                            $elements[] = new Tgallice\FBMessenger\Model\Button\WebUrl($bbcode->configuration_array['web_button']['web_button_web_title_' . $i], $bbcode->configuration_array['web_button']['web_button_web_url_' . $i]);
                        }
                    }

                    $template = null;

                    if (isset($bbcode->configuration_array['web_button']['web_button_message']) && !empty($bbcode->configuration_array['web_button']['web_button_message'])) {
                        $template = new Tgallice\FBMessenger\Model\Attachment\Template\Button($bbcode->configuration_array['web_button']['web_button_message'], $elements);
                    }

                    $ret = str_replace('[' . $bbcode->bbcode . ']','[split_img]', $ret);

                    $imagesAttatchements[] = $template;

                } elseif (isset($bbcode->configuration_array['bbcode_button_type']) && $bbcode->configuration_array['bbcode_button_type'] == 'web_button_generic') {

                    $elements = array();

                    for ($i = 1; $i <= 10; $i++) {
                        if (isset($bbcode->configuration_array['web_button_gen']['web_gen_button_title_' . $i]) && !empty($bbcode->configuration_array['web_button_gen']['web_gen_button_title_' . $i])) {

                            $buttons = array();

                            for ($n = 1; $n <= 3; $n++) {
                                if (isset($bbcode->configuration_array['web_button_gen']['web_button_web_title_' . $i . '_' . $n]) && !empty($bbcode->configuration_array['web_button_gen']['web_button_web_title_' . $i . '_' . $n])) {
                                    $buttons[] = new Tgallice\FBMessenger\Model\Button\WebUrl($bbcode->configuration_array['web_button_gen']['web_button_web_title_' . $i . '_' . $n], $bbcode->configuration_array['web_button_gen']['web_button_web_url_' . $i . '_' . $n]);
                                }
                            }

                            if (empty($buttons)) {
                                $buttons = null;
                            }

                            $elements[] = new Tgallice\FBMessenger\Model\Attachment\Template\Generic\Element(
                                $bbcode->configuration_array['web_button_gen']['web_gen_button_title_' . $i],
                                isset($bbcode->configuration_array['web_button_gen']['web_gen_button_subtitle_' .$i]) && !empty($bbcode->configuration_array['web_button_gen']['web_gen_button_subtitle_' .$i]) ? $bbcode->configuration_array['web_button_gen']['web_gen_button_subtitle_' .$i] : null,
                                isset($bbcode->configuration_array['web_button_gen']['web_gen_button_img_' .$i]) && !empty($bbcode->configuration_array['web_button_gen']['web_gen_button_img_' .$i]) ? $bbcode->configuration_array['web_button_gen']['web_gen_button_img_' .$i] : null,
                                $buttons,
                                isset($bbcode->configuration_array['web_button_gen']['web_gen_button_def_url_' .$i]) && !empty($bbcode->configuration_array['web_button_gen']['web_gen_button_def_url_' .$i]) ? new Tgallice\FBMessenger\Model\DefaultAction($bbcode->configuration_array['web_button_gen']['web_gen_button_def_url_' .$i]) : null
                            );
                        }
                    }

                    $template = null;

                    if (!empty($elements)){
                        $template = new Tgallice\FBMessenger\Model\Attachment\Template\Generic($elements);
                    }

                    $ret = str_replace('[' . $bbcode->bbcode . ']','[split_img]', $ret);

                    $imagesAttatchements[] = $template;

                } elseif (isset($bbcode->configuration_array['bbcode_button_type']) && $bbcode->configuration_array['bbcode_button_type'] == 'web_button_ellist') {

                    $elements = array();

                    for ($i = 1; $i <= 4; $i++) {
                        if (isset($bbcode->configuration_array['web_button_list']['web_list_title_' . $i]) && !empty($bbcode->configuration_array['web_button_list']['web_list_title_' . $i])) {
                            $elements[] = new Tgallice\FBMessenger\Model\Attachment\Template\ElementList\Element(
                                $bbcode->configuration_array['web_button_list']['web_list_title_' . $i],
                                isset($bbcode->configuration_array['web_button_list']['web_list_sub_title_' .$i]) && !empty($bbcode->configuration_array['web_button_list']['web_list_sub_title_' .$i]) ? $bbcode->configuration_array['web_button_list']['web_list_sub_title_' .$i] : null,
                                isset($bbcode->configuration_array['web_button_list']['web_list_sub_img_' .$i]) && !empty($bbcode->configuration_array['web_button_list']['web_list_sub_img_' .$i]) ? $bbcode->configuration_array['web_button_list']['web_list_sub_img_' .$i] : null,
                                isset($bbcode->configuration_array['web_button_list']['web_list_button_web_title_' .$i]) && !empty($bbcode->configuration_array['web_button_list']['web_list_button_web_title_' .$i]) ? new Tgallice\FBMessenger\Model\Button\WebUrl($bbcode->configuration_array['web_button_list']['web_list_button_web_title_' . $i], $bbcode->configuration_array['web_button_list']['web_list_button_web_url_' . $i]) : null,
                                isset($bbcode->configuration_array['web_button_list']['web_list_def_url_' .$i]) && !empty($bbcode->configuration_array['web_button_list']['web_list_def_url_' .$i]) ? new Tgallice\FBMessenger\Model\DefaultAction($bbcode->configuration_array['web_button_list']['web_list_def_url_' .$i], Tgallice\FBMessenger\Model\DefaultAction::HEIGHT_RATIO_FULL) : null
                            );
                        }
                    }

                    $template = null;

                    if (!empty($elements)) {
                        $template = new Tgallice\FBMessenger\Model\Attachment\Template\ElementList($elements,
                            new Tgallice\FBMessenger\Model\Button\WebUrl($bbcode->configuration_array['web_button_list']['web_list_button_default_web_title'], $bbcode->configuration_array['web_button_list']['web_list_button_default_web_url']),
                            Tgallice\FBMessenger\Model\Attachment\Template\ElementList::TOP_STYLE_COMPACT
                        );
                    }

                    $ret = str_replace('[' . $bbcode->bbcode . ']','[split_img]', $ret);

                    $imagesAttatchements[] = $template;
                }
            }
        }


	    $parts = explode('[split_img]', $ret);

	    $messages = array();

	    // Keep messages order as it was
	    foreach ($parts as $key => $part)
	    {
	        if (!empty(trim($part))) {
	            $messages[] = new Tgallice\FBMessenger\Model\Message($part);
	        }

	        if (isset($imagesAttatchements[$key])) {
	            $messages[] = $imagesAttatchements[$key];
	        }       
	    }
	    
	    return $messages;
	}

    public function processEchoMessage($eventMessage)
    {

        $messageEcho = $eventMessage->getMessageEcho();

        if ($messageEcho->getAppId() !== null && $messageEcho->getAppId() == $this->settings['app_settings']['app_id'] ) {
            return;
        }

        // User ID
        $userId = $eventMessage->getRecipientId();

        // Check is user id blocked
        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $userId))) > 0) {
            exit;
        }

        // Recipient User ID
        $recipientUserId = $eventMessage->getSenderId ();

        $fbChat = erLhcoreClassModelFBChat::findOne ( array (
            'filter' => array (
                'user_id' => $userId,
                'recipient_user_id' => $recipientUserId,
                'page_id' => $this->getPage()->id
            )
        ) );

        $db = ezcDbInstance::get();

        if (!($fbChat instanceof erLhcoreClassModelFBChat)) {
            return;
        }

        $chat = $fbChat->chat;

        if (!($chat instanceof erLhcoreClassModelChat)) {
            return;
        }

        try {
            $db->beginTransaction();

            $messageText = null;

            $message = $eventMessage->getMessageEcho();

            if ($message->hasText()) {
                $messageText = $message->getText();
            } elseif ($message->hasAttachments()) {

                $attatchements = $message->getAttachments();

                foreach ($attatchements as $data) {
                    if ($data['type'] == 'file') {
                        $messageText .= '[url=' . $data['payload']['url'].']Download file[/url]';
                    } else if ($data['type'] == 'image') {
                        $messageText .= '[img]' . $data['payload']['url'].'[/img]';
                    } else if ($data['type'] == 'location') {
                        $messageText .= '[url=' . $data['url'].']' . $data['title'] . '[/url] (' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].')[loc]' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].'[/loc]';
                    } else if ($data['type'] == 'fallback') {
                        $messageText .= '[url=' . $data['url'] . ']' . $data['title'] . '[/url]';
                    } else {
                        $messageText .= 'Unknown type - '.json_encode($data);
                    }
                }
            }

            /**
             * Store new message
             */
            $msg = new erLhcoreClassModelmsg ();
            $msg->msg = trim ( $messageText );
            $msg->chat_id = $chat->id;
            $msg->user_id = -2;
            $msg->name_support = 'Page admin';
            $msg->time = time ();

            erLhcoreClassChat::getSession ()->save ( $msg );

            $chat->last_msg_id = $msg->id;
            $chat->last_user_msg_time = $msg->time;
            $chat->saveThis ();

            $db->commit();

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive',array('chat' => & $chat, 'msg' => & $msg));

        } catch (Exception $e) {
            $db->rollback();
            erLhcoreClassLog::write(print_r($e->getMessage(), true));
        }

    }


    public function processCallbackDefault($eventMessage)
    {
        // User ID
        $userId = $eventMessage->getSenderId ();

        // Check is user id blocked
        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $userId))) > 0) {
            exit;
        }

        // Recipient User ID
        $recipientUserId = $eventMessage->getRecipientId();

        $fbChat = erLhcoreClassModelFBChat::findOne ( array (
            'filter' => array (
                'user_id' => $userId,
                'recipient_user_id' => $recipientUserId,
                'page_id' => $this->getPage()->id
            )
        ) );

        $db = ezcDbInstance::get();

        if (!($fbChat instanceof erLhcoreClassModelFBChat)) {
            $fbChat = new erLhcoreClassModelFBChat();
        }

        $chat = $fbChat->chat;

        // fix https://github.com/LiveHelperChat/fbmessenger/issues/1
        // If chat is closed make it pending again
        if ($chat instanceof erLhcoreClassModelChat && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
            $chat->pnd_time = time();
            $chat->status_sub_sub = 2;
            $chat->saveThis();
        }

        $needSave = false;

        if (! ($chat instanceof erLhcoreClassModelChat)) {
            try {
                $needSave = true;

                $db->beginTransaction();

                $chat = new erLhcoreClassModelChat ();

                // Set default department
                $department = erLhcoreClassModelDepartament::fetch($this->getPage()->dep_id);

                // Assign department from page configuration
                $chat->dep_id = $department->id;
                $chat->priority = $department->priority;

                // Just save and send fb message if it's facebook chat
                $dataArray = array (
                    'fb_chat' => true
                );

                $nick = 'FB Visitor - ' . $userId;

                try {
                    $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->access_token);
                    $profile = $messenger->getUserProfile($eventMessage->getSenderId());
                    $dataArray['fb_gender'] = $profile->getGender();
                    $dataArray['fb_locale'] = $profile->getLocale();
                    $nick = trim($profile->getFirstName() . ' ' . $profile->getLastName());
                } catch (Exception $e) {
                    erLhcoreClassLog::write($e->getMessage());
                }

                $chat->nick = $nick;
                $chat->time = time ();
                $chat->status = 0;
                $chat->hash = erLhcoreClassChat::generateHash ();
                $chat->referrer = '';
                $chat->session_referrer = '';
                $chat->chat_variables = json_encode ( $dataArray );

                $chat->saveThis ();

                $msgInitial = new erLhcoreClassModelmsg();
                $msgInitial->msg = "Facebook user started a chat.";
                $msgInitial->chat_id = $chat->id;
                $msgInitial->user_id = -1;
                $msgInitial->time = time ();
                $msgInitial->saveThis();

                /*$messageText = null;

                $message = $eventMessage->getMessage();

                if ($message->hasText()) {
                    $messageText = $message->getText();
                } elseif ($message->hasAttachments()) {

                    $attatchements = $message->getAttachments();

                    foreach ($attatchements as $data) {
                        if ($data['type'] == 'file') {
                            $messageText .= '[url=' . $data['payload']['url'].']Download file[/url]';
                        } else if ($data['type'] == 'image') {
                            $messageText .= '[img]' . $data['payload']['url'].'[/img]';
                        } else {
                            $messageText .= 'Unknown type - '.json_encode($data);
                        }
                    }
                }*/

                /**
                 * Store new message
                 */
                /*$msg = new erLhcoreClassModelmsg ();
                $msg->msg = trim ( $messageText );
                $msg->chat_id = $chat->id;
                $msg->user_id = 0;
                $msg->time = time ();

                erLhcoreClassChat::getSession ()->save ( $msg );*/

                $chat->last_msg_id = $msgInitial->id;
                $chat->last_user_msg_time = $msgInitial->time;
                $chat->saveThis ();

                $db->commit();
                
                /**
                 * Execute standard callback as chat was started
                 */
                erLhcoreClassChatEventDispatcher::getInstance ()->dispatch ( 'chat.chat_started', array (
                    'chat' => & $chat,
                    'msg' => $msgInitial,
                    'fb_user_id' => $userId
                ) );

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        // Update FB Chat if required
        if ($needSave == true || $fbChat->user_id == 0 || $fbChat->chat_id == 0)
        {
            try {
                $db->beginTransaction();

                $fbChat->user_id = $userId;
                $fbChat->recipient_user_id = $recipientUserId;
                $fbChat->chat_id = $chat->id;
                $fbChat->ctime = time();
                $fbChat->page_id = $this->getPage()->id;
                $fbChat->saveOrUpdate();

                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }

	public function processVisitorDelete($eventMessage, $provider = 'facebook') {
        // User ID
        $userId = $eventMessage->getSenderId ();

        // Check is user id blocked
        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $userId))) > 0) {
            exit;
        }

        // Recipient User ID
        $recipientUserId = $eventMessage->getRecipientId();

        $fbChat = erLhcoreClassModelFBChat::findOne(array(
            'filter' => array (
                'user_id' => $userId,
                'recipient_user_id' => $recipientUserId,
                'page_id' => $this->getPage()->id
            )
        ));

        if ($fbChat instanceof erLhcoreClassModelFBChat) {
            $chat = $fbChat->chat;
            if ($chat instanceof erLhcoreClassModelChat) {
                $message = $eventMessage->getMessageDelete();
                $db = ezcDbInstance::get();
                $msg = erLhcoreClassModelmsg::findOne(['customfilter' => ['JSON_EXTRACT(meta_msg,\'$.emid\') = ' . $db->quote($message->getId())],'filter' => ['chat_id' => $chat->id]]);
                if ($msg instanceof erLhcoreClassModelmsg) {
                    $msg->msg = 'Message was deleted by visitor!';
                    $msg->user_id = -1;
                    $msg->updateThis(['msg','user_id']);

                    // Update operator message
                    $chat->operation_admin .= "lhinst.updateMessageRowAdmin({$msg->chat_id},{$msg->id});";
                    $chat->updateThis(['update' => ['operation_admin']]);
                }
            }
        }
    }

	public function processVisitorMessage($eventMessage, $provider = 'facebook') {

	    // User ID
		$userId = $eventMessage->getSenderId ();

        // Check is user id blocked
        if (erLhcoreClassModelChatBlockedUser::getCount(array('filter' => array('ip' => $userId))) > 0) {
            exit;
        }

		// Recipient User ID
		$recipientUserId = $eventMessage->getRecipientId();

		$fbChat = erLhcoreClassModelFBChat::findOne ( array (
				'filter' => array (
						'user_id' => $userId,
				        'recipient_user_id' => $recipientUserId,
				        'page_id' => $this->getPage()->id
				) 
		) );

		$db = ezcDbInstance::get();
		
		if (!($fbChat instanceof erLhcoreClassModelFBChat)) {
			$fbChat = new erLhcoreClassModelFBChat();
		}
				
		$chat = $fbChat->chat;
		
		// fix https://github.com/LiveHelperChat/fbmessenger/issues/1
		// If chat is closed make it pending again
		if ($chat instanceof erLhcoreClassModelChat && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
		    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;	
		    $chat->pnd_time = time();
		    $chat->status_sub_sub = 2;
		    $chat->saveThis();
		}
		
		$needSave = false;
				
		if (! ($chat instanceof erLhcoreClassModelChat)) {
			try {
				$needSave = true;
				
				$db->beginTransaction();
			
				$chat = new erLhcoreClassModelChat ();

				// Set default department
				$department = erLhcoreClassModelDepartament::fetch($this->getPage()->dep_id);
				
				// Assign department from page configuration
				$chat->dep_id = $department->id;
				$chat->priority = $department->priority;

				// Just save and send fb message if it's facebook chat
				$dataArray = array (
						'fb_chat' => true
				);

				$nick = ($provider == 'instagram' ? 'Instagram' : 'FB') . ' Visitor - ' . $userId;

				$initMessage = false;

                try {

                    if ($provider == 'instagram') {
                        $dataArray['fb_chat_type'] = 'instagram';
                        $dataArray['fb_chat_stop'] = true;
                        $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->access_token);
                        $profile = $messenger->getUserProfile($eventMessage->getSenderId(),['name']);
                        $nick = trim($profile->getName());
                    } else {
                        $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->access_token);
                        $profile = $messenger->getUserProfile($eventMessage->getSenderId());
                        $dataArray['fb_gender'] = $profile->getGender();
                        $dataArray['fb_locale'] = $profile->getLocale();
                        $nick = trim($profile->getFirstName() . ' ' . $profile->getLastName());
                    }

                    $initMessage = true;

                } catch (Exception $e) {
                    erLhcoreClassLog::write($e->getMessage());
                }

				
				$chat->nick = $nick;
				$chat->time = time ();
				$chat->status = 0;
				$chat->hash = erLhcoreClassChat::generateHash ();
				$chat->referrer = '';
				$chat->session_referrer = '';
				$chat->chat_variables = json_encode ( $dataArray );
				
				$chat->saveThis ();
				
				if ($initMessage == true) {
				    $msgInitial = new erLhcoreClassModelmsg();
				    $msgInitial->msg = ($provider == 'instagram' ? 'Instagram' : 'Facebook') . " user started a chat.";
				    $msgInitial->chat_id = $chat->id;
				    $msgInitial->user_id = -1;
				    $msgInitial->time = time ();
				    $msgInitial->saveThis();
				}
				
				$messageText = null;
				
				$message = $eventMessage->getMessage();
				
				if ($message->hasText()) {
				    $messageText = $message->getText();
				} elseif ($message->hasAttachments()) {	
				    			     
				    $attatchements = $message->getAttachments();
				    				     
				    foreach ($attatchements as $data) {
				        if ($data['type'] == 'file') {
				            $messageText .= '[url=' . $data['payload']['url'].']Download file[/url]';
				        } else if ($data['type'] == 'image') {
				            $messageText .= '[img]' . $data['payload']['url'].'[/img]';
                        } else if ($data['type'] == 'location') {
                            $messageText .= '[url=' . $data['url'].']' . $data['title'] . '[/url] (' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].')[loc]' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].'[/loc]';
				        } else if ($data['type'] == 'fallback') {
                            $messageText .= '[url=' . $data['url'] . ']' . $data['title'] . '[/url]';
                        } else {
				            $messageText .= 'Unknown type - '.json_encode($data);
				        }
				    }
				}
				
				/**
				 * Store new message
				 */
				$msg = new erLhcoreClassModelmsg ();
				$msg->msg = trim ( $messageText );
				$msg->chat_id = $chat->id;
				$msg->user_id = 0;
				$msg->time = time ();
                $msg->meta_msg = json_encode(['emid' => $message->getId()]);

				erLhcoreClassChat::getSession ()->save ( $msg );
				
				$chat->last_msg_id = $msg->id;
				$chat->last_user_msg_time = $msg->time;
				$chat->saveThis ();

                $db->commit();

				/**
				 * Execute standard callback as chat was started
				 */
				erLhcoreClassChatEventDispatcher::getInstance ()->dispatch ( 'chat.chat_started', array (
						'chat' => & $chat,
						'msg' => $msg ,
                        'fb_user_id' => $userId
				) );

				
			} catch (Exception $e) {
				$db->rollback();
				throw $e;
			}					
		} else {
			
			try {				
				$db->beginTransaction();
				
				$messageText = null;
				
				$message = $eventMessage->getMessage();
				
				if ($message->hasText()) {
				    $messageText = $message->getText();
			    } elseif ($message->hasAttachments()) {
			        
			        $attatchements = $message->getAttachments();
			        
			        foreach ($attatchements as $data) {
			            if ($data['type'] == 'file') {
			                $messageText .= '[url=' . $data['payload']['url'].']Download file[/url]';
			            } else if ($data['type'] == 'image') {
			                $messageText .= '[img]' . $data['payload']['url'].'[/img]';
                        } else if ($data['type'] == 'location') {
                            $messageText .= '[url=' . $data['url'].']' . $data['title'] . '[/url] (' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].')[loc]' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].'[/loc]';
			            } else if ($data['type'] == 'fallback') {
                            $messageText .= '[url=' . $data['url'] . ']' . $data['title'] . '[/url]';
                        } else {
			                $messageText .= 'Unknown type - '.json_encode($data);
			            }
			        }
			    }
				    
				/**
				 * Store new message
				 */
				$msg = new erLhcoreClassModelmsg ();
				$msg->msg = trim ( $messageText );
				$msg->chat_id = $chat->id;
				$msg->user_id = 0;
				$msg->time = time ();
                $msg->meta_msg = json_encode(['emid' => $message->getId()]);

                erLhcoreClassLog::write(print_r($message, true)) . "\n";
				 
				erLhcoreClassChat::getSession ()->save ( $msg );

				if ((erLhcoreClassChat::isOnline($chat->dep_id) == false) || ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT && $chat->last_op_msg_time < time()-1600)) {
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                }

				$chat->last_msg_id = $msg->id;
				$chat->last_user_msg_time = $msg->time;
				$chat->saveThis ();		
				
				$db->commit();
				
				erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive',array('chat' => & $chat, 'msg' => & $msg));
				
			} catch (Exception $e) {
				$db->rollback();
				throw $e;
			}
		}

		// Update FB Chat if required
		if ($needSave == true || $fbChat->user_id == 0 || $fbChat->chat_id == 0)
		{
			try {
				$db->beginTransaction();

				$fbChat->user_id = $userId;
				$fbChat->recipient_user_id = $recipientUserId;
				$fbChat->chat_id = $chat->id;
				$fbChat->ctime = time();
				$fbChat->page_id = $this->getPage()->id;
				$fbChat->saveOrUpdate();

				$db->commit();				
			} catch (Exception $e) {
				$db->rollback();
				throw $e;
			}
		}
	}
	
	public function deleteChat($params)
	{
	    $fbChat = erLhcoreClassModelFBChat::findOne ( array (
	        'filter' => array (
	            'chat_id' => $params['chat']->id
	        )
	    ) );
	    
	    if ($fbChat instanceof erLhcoreClassModelFBChat) {
	        $fbChat->removeThis();
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
            'erLhcoreClassModelFBChat'  => 'extension/fbmessenger/classes/erlhcoreclassmodelfbchat.php',
            'erLhcoreClassModelFBPage'  => 'extension/fbmessenger/classes/erlhcoreclassmodelfbpage.php',
            'erLhcoreClassModelFBBBCode'=> 'extension/fbmessenger/classes/erlhcoreclassmodelfbbbcode.php',
            'erLhcoreClassFBValidator'                          => 'extension/fbmessenger/classes/erlhcoreclassfbvalidator.php',
            'erLhcoreClassModelFBMessengerUser'                 => 'extension/fbmessenger/classes/erlhcoreclassmodelfbuser.php',
            'erLhcoreClassModelMyFBPage'                        => 'extension/fbmessenger/classes/erlhcoreclassmodelmyfbpage.php',
            'erLhcoreClassModelFBLead'                          => 'extension/fbmessenger/classes/erlhcoreclassmodelfblead.php',
            'erLhcoreClassModelFBNotificationSchedule'          => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationschedule.php',
            'erLhcoreClassModelFBNotificationStatus'            => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationstatus.php',
            'erLhcoreClassModelFBNotificationScheduleCampaign'  => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationschedulecampaign.php',
            'erLhcoreClassModelFBNotificationScheduleItem'      => 'extension/fbmessenger/classes/erlhcoreclassmodelfbnotificationscheduleitem.php',
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


