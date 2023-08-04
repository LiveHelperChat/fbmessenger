<?php
class erLhcoreClassExtensionFbmessenger {
    
	public function __construct() {
	    
	}
	
	public function run() {
		$this->registerAutoload ();
		
		include_once 'extension/fbmessenger/vendor/autoload.php';
		
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

		$dispatcher->listen('telegram.msg_received', array(
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


        // Elastic Search
        $dispatcher->listen('system.getelasticstructure', array(
            $this,'getElasticStructure')
        );

        $dispatcher->listen('elasticsearch.indexchat', array(
            $this,'indexChat')
        );

        $dispatcher->listen('elasticsearch.getstate', array(
            $this,'getState')
        );

        $dispatcher->listen('elasticsearch.getpreviouschats', array(
            $this, 'getPreviousChatsFilter')
        );
        
        $dispatcher->listen('elasticsearch.getpreviouschats_abstract', array(
            $this, 'getPreviousChatsFilter')
        );

        // Handle canned messages custom workflow
        $dispatcher->listen('chat.canned_msg_before_save', array(
            $this, 'cannedMessageValidate')
        );

        $dispatcher->listen('chat.before_newcannedmsg', array(
            $this, 'cannedMessageValidate')
        );

        $dispatcher->listen('chat.workflow.canned_message_replace', array(
            $this, 'cannedMessageReplace')
        );

        $dispatcher->listen('elasticsearch.chatsearchattr', array(
            $this, 'appendSearchAttr')
        );

        $dispatcher->listen('elasticsearch.chatsearchexecute',array(
            $this, 'chatSearchExecute')
        );

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

    // Always auto preload telegram chats
    public function autoPreload($params) {

        $chatVariables = $params['chat']->chat_variables_array;

        if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1)
        {
            $params['load_previous'] = 1;
        }
    }

	public function cannedMessageReplace($params)
    {
        $chatVariables = $params['chat']->chat_variables_array;

        if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1)
        {
            foreach ($params['items'] as & $item) {

                if ($params['chat']->locale != '' && $item->languages != '') {
                    // Override language by chat locale
                    $languages = json_decode($item->languages, true);

                    if (is_array($languages)) {
                        foreach ($languages as & $lang) {

                            if (isset($lang['message_lang_fb']) && !empty($lang['message_lang_fb'])) {
                                $lang['message'] = $lang['message_lang_fb'];
                            }

                            if (isset($lang['fallback_message_lang_fb']) && !empty($lang['fallback_message_lang_fb'])) {
                                $lang['fallback_msg'] = $lang['fallback_message_lang_fb'];
                            }
                        }
                    }

                    $item->languages = json_encode($languages);
                }

                $additionalData = $item->additional_data_array;

                if (isset($additionalData['message_fb']) && !empty($additionalData['message_fb'])) {
                    $item->msg = $additionalData['message_fb'];
                }

                if (isset($additionalData['fallback_fb']) && !empty($additionalData['fallback_fb'])) {
                    $item->fallback_msg = $additionalData['fallback_fb'];
                }
            }
        }
    }

    public static function appendSearchAttr($params)
    {
        $extFb = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

        $params['attr']['filterAttributes'][$extFb->settings['elastic_search']['search_attr']] = array (
            'type' => 'text',
            'required' => false,
            'valid_if_filled' => false,
            'filter_type' => 'filter',
            'filter_table_field' => $extFb->settings['elastic_search']['search_attr'],
            'validation_definition' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 1)
            )
        );
    }

    public static function chatSearchExecute($params)
    {
        $extFb = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

        if ($params['filter']['input_form']->{$extFb->settings['elastic_search']['search_attr']} == 1) {
            $params['sparams']['body']['query']['bool']['must'][]['range']['fb_user_id']['gt'] = 0;
        }
    }

	public function cannedMessageValidate($params)
    {
        $definition = array(
            'MessageExtFB' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
            'FallbackMessageExtFB' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),

            'message_lang_fb' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
            'fallback_message_lang_fb' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY)
        );

        $form = new ezcInputForm( INPUT_POST, $definition );

        $langArray = array();
        foreach ($params['msg']->languages_array as $index => $langData) {
            $langData['message_lang_fb'] = $form->message_lang_fb[$index];
            $langData['fallback_message_lang_fb'] = $form->fallback_message_lang_fb[$index];
            $langArray[] = $langData;
        }

        $params['msg']->languages = json_encode($langArray);
        $params['msg']->languages_array = $langArray;

        // Store additional data
        $additionalArray =  $params['msg']->additional_data_array;

        if ( $form->hasValidData( 'MessageExtFB' )) {
            $additionalArray['message_fb'] = $form->MessageExtFB;
        }

        if ( $form->hasValidData( 'FallbackMessageExtFB' ) )
        {
            $additionalArray['fallback_fb'] = $form->FallbackMessageExtFB;
        }

        $params['msg']->additional_data = json_encode($additionalArray);
        $params['msg']->additional_data_array = $additionalArray;
    }

	public function getPreviousChatsFilter($params)
    {
        $chatVariables = json_decode($params['chat']->chat_variables,true);

        if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1 && isset($chatVariables['fb_user_id']) && is_numeric($chatVariables['fb_user_id']))
        {
            $params['sparams']['body']['query']['bool']['must'] = array();
            $params['sparams']['body']['query']['bool']['must'][]['term']['fb_user_id'] = (int)$chatVariables['fb_user_id'];
            $params['sparams']['body']['query']['bool']['must'][]['range']['chat_id']['lt'] = $params['chat']->id;
        }
    }

    // Get elastic structure
    public function getElasticStructure($params)
    {
        $params['structure'][(isset($params['index_new']) ? $params['index_new'] : 'chat')]['types']['lh_chat']['fb_user_id'] = array('type' => 'long');
        $params['structure'][(isset($params['index_new']) ? $params['index_new'] : 'chat')]['types']['lh_chat']['fb_page_id'] = array('type' => 'long');
    }

    // Index chat
    public function indexChat($params)
    {
        $chatVariables = json_decode($params['chat']->chat_variables,true);

        if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1 && isset($chatVariables['fb_user_id']) && isset($chatVariables['fb_page_id']))
        {
            $params['chat']->fb_user_id = $chatVariables['fb_user_id'];
            $params['chat']->fb_page_id = $chatVariables['fb_page_id'];
        }
    }

    public function getState($params)
    {
        if (isset($params['chat']->fb_user_id) && is_numeric($params['chat']->fb_user_id)) {
            $params['state']['fb_user_id'] = $params['chat']->fb_user_id;
        } else {
            $params['state']['fb_user_id'] = 0;
        }

        if (isset($params['chat']->fb_page_id) && is_numeric($params['chat']->fb_page_id)) {
            $params['state']['fb_page_id'] = $params['chat']->fb_page_id;
        } else {
            $params['state']['fb_page_id'] = 0;
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
	
	public function sendMessageToFb($params)
	{
    	$chatVariables = $params['chat']->chat_variables_array;
    	
    	if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1)
    	{
    	    try {    	        
    	        $chat = erLhcoreClassModelFBChat::findOne(array('filter' => array('chat_id' => $params['chat']->id)));

    	        // Check does chat still exists
    	        if ($chat instanceof erLhcoreClassModelFBChat)
                {
                    $this->setPage($chat->page);

                    if ($this->getPage()->verified == 1) {
                        $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->page_token);

                        $messages = self::parseMessageForFB($params['msg']->msg, $params['msg']->meta_msg_array, $params['msg']->id, array('messenger' => $messenger, 'chat' => $chat));

                        foreach ($messages as $msg) {
                            if ($msg !== null) {
                                $response = $messenger->sendMessage($chat->user_id, $msg);
                            }
                        }

                        // I could not force fb to send me message delivery status while in dep mode of the app.
                        // So I just mark those messages as send
                        $params['msg']->del_st = erLhcoreClassModelmsg::STATUS_READ;
                        $params['msg']->saveThis(['update' => ['del_st']]);
                    }
                }
        	    
    	    } catch (Exception $e) {

                // I could not force fb to send me message delivery status while in dep mode of the app.
                // So I just mark those messages as send
                $params['msg']->del_st = erLhcoreClassModelmsg::STATUS_REJECTED;
                $params['msg']->saveThis(['update' => ['del_st']]);

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

        // LHC Core set's message to pending if it's setting remote ID
        /*if (is_object($params['chat']->incoming_chat) &&
            is_object($params['chat']->incoming_chat->incoming) &&
            $params['chat']->incoming_chat->incoming->scope == 'facebookwhatsappscope' &&
            $params['msg']->user_id != -1 && ($params['msg']->user_id > 0 || $params['msg']->user_id == -2)
        ) {
            $params['msg']->del_st = erLhcoreClassModelmsg::STATUS_PENDING;
            $params['msg']->updateThis(['update' => ['del_st']]);
        }*/
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
	public static function parseMessageForFB($ret, $metaMessageData = array(), $messageId = 0, $paramsExecution = array())
	{	    
	    $matches = array();
	    
	    // Allow extensions to preparse send message
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('fbmessenger.before_parse_send', array('msg' => & $ret));

        $ret = str_replace(array('[list]','[/list]','[*]','[b]','[/b]','[i]','[/i]','[u]','[/u]','[s]','[/s]'),array('','','','','','','','','','',''),$ret);

	    preg_match_all('/\[img\](.*?)\[\/img\]/ms', $ret, $matches);
	    
	    // Parse Images
	    $imagesAttatchements = array();

	    foreach ($matches[1] as $key => $img) {
	        $in = trim($img);

	        $url = erLhcoreClassBBCode::esc_url($in);

	        if ( empty($url) )
	            continue;

	        $image = new Tgallice\FBMessenger\Model\Attachment\Image(urldecode(ltrim($url,"/")));
	        
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

	            // Check that user has permission to see the chat. Let say if user purposely types file bbcode
	            if ($hash == $file->security_hash) {


                    if (in_array($file->extension,['png','jpeg','jpg','gif','bmp'])) {

                        $url = erLhcoreClassBBCodePlain::_make_url_file([1 => $fileKey]);

                        $url = erLhcoreClassBBCode::esc_url($url);

                        if ( empty($url) ) {
                            continue;
                        }

                        $template = new Tgallice\FBMessenger\Model\Attachment\Image(urldecode(ltrim($url,"/")));

                    } else {
                        $elements = [
                            new Tgallice\FBMessenger\Model\Button\WebUrl(erTranslationClassLhTranslation::getInstance()->getTranslation('file/file','Download'), 'https://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('file/downloadfile')."/{$file->id}/{$hash}" )
                        ];
                        $template = new Tgallice\FBMessenger\Model\Attachment\Template\Button(erTranslationClassLhTranslation::getInstance()->getTranslation('file/file','Download').' - '.htmlspecialchars($file->upload_name).' ['.$file->extension.']', $elements);
                    }

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

	    if (!isset($metaMessageData['content']['quick_replies']) && !isset($metaMessageData['content']['buttons_generic']) && !isset($metaMessageData['content']['generic']))
        {
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
        }

	    /**
	     * Over time we should add more element to supported list.
         */
        if (is_array($metaMessageData) && !isset($metaMessageData['processed']) || $metaMessageData['processed'] == false) {
            if (isset($metaMessageData['content']) && is_array($metaMessageData['content']) && !empty($metaMessageData['content'])) {
                foreach ($metaMessageData['content'] as $type => $metaMessage) {
                    if ($type == 'quick_replies' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) {

                        $elements = [];
                        foreach ($metaMessage as $item) {
                            $disabledButton = isset($item['content']['disabled']) && $item['content']['disabled'] == true;
                            if ($disabledButton == false) {
                                if ($item['type'] == 'url') {
                                    $elements[] = new Tgallice\FBMessenger\Model\QuickReply\Text($item['content']['name'], $item['content']['payload']);
                                } elseif ($item['type'] == 'trigger') {
                                    $elements[] = new Tgallice\FBMessenger\Model\QuickReply\Text($item['content']['name'], 'trigger__'.$item['content']['payload']. '__' . md5($item['content']['name']) . '__' . $messageId );
                                } elseif ($item['type'] == 'updatechat') {
                                    // This scenario is not supported in general
                                } else {
                                    $elements[] = new Tgallice\FBMessenger\Model\QuickReply\Text($item['content']['name'], 'bpayload__'.$item['content']['payload']. '__' . md5($item['content']['name']) . '__' . $messageId );
                                }
                            }
                       }

                       $messageReply = new Tgallice\FBMessenger\Model\Message($ret);
                       $messageReply->setQuickReplies($elements);

                       $messages[] = $messageReply;

                    } elseif ($type == 'dropdown' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) {
                        // @todo to implement
                    } elseif ($type == 'buttons' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) {
                        // @todo to implement
                    } elseif ($type == 'custom' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) {
                        // @todo to implement
                    } elseif ($type == 'collected_summary') {
                        // @todo to implement
                    } elseif ($type == 'buttons_generic') {
                        $elements = [];
                        foreach ($metaMessage as $item) {
                            $disabledButton = isset($item['content']['disabled']) && $item['content']['disabled'] == true;
                            if ($disabledButton == false) {
                                if ($item['type'] == 'url') {
                                    $elements[] = new Tgallice\FBMessenger\Model\Button\WebUrl($item['content']['name'], $item['content']['payload']);
                                } elseif ($item['type'] == 'trigger') {
                                    $elements[] = new Tgallice\FBMessenger\Model\Button\Postback($item['content']['name'], 'trigger__'.$item['content']['payload']. '__' . md5($item['content']['name']) . '__' . $messageId );
                                } elseif ($item['type'] == 'updatechat') {
                                    // This scenario is not supported in general
                                } else {
                                    $elements[] = new Tgallice\FBMessenger\Model\Button\Postback($item['content']['name'], 'bpayload__'.$item['content']['payload']. '__' . md5($item['content']['name']) . '__' . $messageId );
                                }
                            }
                        }
                        $messages[] = new Tgallice\FBMessenger\Model\Attachment\Template\Button($ret, $elements);
                    } elseif ($type == 'generic') {

                        $elements = [];
                        foreach ($metaMessage['items'] as $item) {
                            $buttons = [];
                            foreach ($item['buttons'] as $button) {
                                if ($button['type'] == 'url') {
                                    $buttons[] = new Tgallice\FBMessenger\Model\Button\WebUrl($button['content']['name'], $button['content']['payload']);
                                } elseif ($button['type'] == 'trigger') {
                                    $buttons[] = new Tgallice\FBMessenger\Model\Button\Postback($button['content']['name'], 'trigger__'.$button['content']['payload']. '__' . md5($button['content']['name']) . '__' . $messageId);
                                } elseif ($item['type'] == 'updatechat') {
                                    // This scenario is not supported in general
                                } else {
                                    $elements[] = new Tgallice\FBMessenger\Model\Button\Postback($item['content']['name'], 'bpayload__'.$button['content']['payload']. '__' . md5($button['content']['name']) . '__' . $messageId );
                                }
                            }

                            if ($item['type'] == 'url') {
                                $elements[] = new Tgallice\FBMessenger\Model\Attachment\Template\Generic\Element(
                                    $item['content']['title'],
                                    $item['content']['subtitle'],
                                    $item['content']['img'],
                                    $buttons,
                                    new Tgallice\FBMessenger\Model\DefaultAction($item['content']['payload'])
                                );
                            } elseif ($item['type'] == 'trigger') {
                                $elements[] = new Tgallice\FBMessenger\Model\Attachment\Template\Generic\Element(
                                    $item['content']['title'],
                                    $item['content']['subtitle'],
                                    $item['content']['img'],
                                    $buttons
                                );
                            } elseif ($item['type'] == 'updatechat') {
                                // This scenario is not supported in general
                            } else {
                                $elements[] = new Tgallice\FBMessenger\Model\Attachment\Template\Generic\Element(
                                    $item['content']['title'],
                                    $item['content']['subtitle'],
                                    $item['content']['img'],
                                    $buttons
                                );
                            }
                        }

                        $messages[] = new Tgallice\FBMessenger\Model\Attachment\Template\Generic($elements);

                    } elseif ($type == 'list') {
                        // @todo implement
                    } elseif ($type == 'typing') {
                        $paramsExecution['messenger']->setTypingStatus($paramsExecution['chat']->user_id, \Tgallice\FBMessenger\TypingIndicator::TYPING_ON);
                        sleep(isset($metaMessage['duration']) && $metaMessage['duration'] < 7 ? (int)$metaMessage['duration'] : 3);
                        $paramsExecution['messenger']->setTypingStatus($paramsExecution['chat']->user_id, \Tgallice\FBMessenger\TypingIndicator::TYPING_OFF);
                    } elseif ($type == 'progress') {
                        //<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/progress.tpl.php'));
                    } elseif ($type == 'html') {
                        //<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/html.tpl.php'));
                    } elseif ($type == 'html_snippet') {
                        //<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/html_snippet.tpl.php'));
                    } elseif ($type == 'execute_js') {
                        //<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/execute_js.tpl.php'));
                    } elseif ($type == 'video') {
                        //<?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/video.tpl.php'));
                    } elseif ($type == 'text_conditional') {
                        // <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/text_conditional.tpl.php'));
                    } elseif ($type == 'attr_options' && (!isset($messagesStats) || $messagesStats['total_messages'] == $messagesStats['counter_messages'])) {
                        //  <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/content/attr_options.tpl.php'));
                    }
                }
            }
        }

	    return $messages;
	}

	public function processVisitorMessage($eventMessage) {
		
	    // User ID
		$userId = $eventMessage->getSenderId ();
		
		// Recipient User ID
		$recipientUserId = $eventMessage->getRecipientId();

		$page = $this->getPage();
        $pageId = $page instanceof erLhcoreClassModelFBPage ? $page->id : $page->page_id;
        $botDisabled = $page->bot_disabled;

		$fbChat = erLhcoreClassModelFBChat::findOne ( array (
				'filter' => array (
						'user_id' => $userId,
				        'recipient_user_id' => $recipientUserId,
				        'page_id' => $pageId,
                        'type' => $page instanceof erLhcoreClassModelFBPage ? 0 : 1
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

		    $fbOptions = erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
            $data = (array)$fbOptions->data;
		    if (!isset($data['new_chat']) || $data['new_chat'] == false)
            {
                if (isset($data['priority']) && $data['priority'] != '' && $data['priority'] != 0) {
                    $chat->priority = isset($data['priority']) ? (int)$data['priority'] : 0;
                }

                if ($chat->gbot_id > 0 && isset($chat->chat_variables_array['gbot_disabled']) && $chat->chat_variables_array['gbot_disabled'] == 1 && $botDisabled == 0) {
                    $chatVariables = $chat->chat_variables_array;
                    unset($chatVariables['gbot_disabled']);
                    $chat->chat_variables_array = $chatVariables;
                    $chat->chat_variables = json_encode($chatVariables);
                }

                if ($chat->gbot_id > 0) {
                    $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
                } else {
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                    $chat->user_id = 0; // fix https://github.com/LiveHelperChat/fbmessenger/issues/6
                    $chat->pnd_time = time();
                }

                $chat->saveThis();
            } else {
                $chat = null;
            }
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

                $fbOptions = erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
                $data = (array)$fbOptions->data;

				if (isset($data['priority']) && $data['priority'] != '' && $data['priority'] != 0) {
                    $chat->priority = isset($data['priority']) ? (int)$data['priority'] : $department->priority;
                } else {
                    $chat->priority = $department->priority;
                }

				// Just save and send fb message if it's facebook chat
				$dataArray = array (
						'fb_chat' => true
				);

				$nick = 'Visitor';

				$initMessage = false;

				if ($page->verified == true)
				{
				    try {
        				$messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->page_token);				
        				$profile = $messenger->getUserProfile($eventMessage->getSenderId());
        				$dataArray['fb_gender'] = $profile->getGender();
        				$dataArray['fb_locale'] = $profile->getLocale();

                        $lead = erLhcoreClassModelFBLead::findOne(array('filter' => array('user_id' => $eventMessage->getSenderId())));

                        if (!($lead instanceof erLhcoreClassModelFBLead)) {
                            $lead = new erLhcoreClassModelFBLead();
                            $lead->user_id = $eventMessage->getSenderId();
                            $lead->first_name = $profile->getFirstName();
                            $lead->last_name = $profile->getLastName();
                            $lead->profile_pic = $profile->getProfilePic();
                            $lead->locale = $profile->getLocale();
                            $lead->timezone = $profile->getTimezone();
                            $lead->gender = $profile->getGender();
                            $lead->is_payment_enabled = 0;
                            $lead->ctime = time();
                            $lead->page_id = $pageId;
                            $lead->type = $page instanceof erLhcoreClassModelFBPage ? 0 : 1;
                            $lead->dep_id = $department->id;
                            $lead->saveThis();
                         } elseif ($lead->blocked == 1) {
                            $lead->blocked = 0;
                            $lead->saveThis();
                        }

                        if (!isset($data['chat_attr']) || $data['chat_attr'] == 0) {
                            $nick = trim($profile->getFirstName() . ' ' . $profile->getLastName());
                        } else {

                            $additionalDataArray = array();

                            if ($lead->first_name != '') {
                                $additionalDataArray[] = array(
                                    'key' => 'Name',
                                    'identifier' => 'firstname',
                                    'value' => $lead->first_name,
                                );
                            }

                            if ($lead->last_name != '') {
                                $additionalDataArray[] = array(
                                    'key' => 'Last name',
                                    'identifier' => 'lastname',
                                    'value' => $lead->last_name,
                                );
                            }

                            if (!empty($additionalDataArray)) {
                                $chat->additional_data_array = $additionalDataArray;
                                $chat->additional_data = json_encode($additionalDataArray);
                            }
                        }

        				$initMessage = true;
        				
				    } catch (Exception $e) {
				        erLhcoreClassLog::write($e->getMessage());
				    }
				}

                $dataArray['fb_user_id'] = $userId;
                $dataArray['fb_page_id'] = $recipientUserId;

				$chat->nick = $nick;
				$chat->time = time ();
				$chat->status = 0;
				$chat->status_sub = 100; // Used to indicate it's facebook chat
				$chat->hash = erLhcoreClassChat::generateHash ();
				$chat->referrer = '';
				$chat->session_referrer = '';
				$chat->chat_variables = json_encode ( $dataArray );
                $chat->pnd_time = time();
				
				$chat->saveThis ();

                $_SERVER['HTTP_USER_AGENT'] = 'API, Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36';

                $eChat = new erLhcoreClassModelChatIncoming();
                $eChat->incoming_id = $recipientUserId;
                $eChat->chat_external_id = $userId.'_'.$pageId.'_'.($page instanceof erLhcoreClassModelFBPage ? 0 : 1);

                erLhcoreClassChatWebhookIncoming::assignOnlineVisitor($chat, $eChat);

				if ($initMessage == true) {
				    $msgInitial = new erLhcoreClassModelmsg();
				    $msgInitial->msg = "Facebook user started a chat.";
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
				        } else if ($data['type'] == 'audio') {
                            $messageText .= '[url=' . $data['payload']['url'].']Download audio[/url]';
				        } else if ($data['type'] == 'image') {
				            $messageText .= '[img]' . $data['payload']['url'].'[/img]';
                        } else if ($data['type'] == 'location') {
                            $messageText .= '[url=' . $data['url'].']' . $data['title'] . '[/url] (' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].')[loc]' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].'[/loc]';
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

				erLhcoreClassChat::getSession ()->save ( $msg );
				
				$chat->last_msg_id = $msg->id;
				$chat->last_user_msg_time = $msg->time;
				$chat->saveThis ();

                $fbChat->user_id = $userId;
                $fbChat->recipient_user_id = $recipientUserId;
                $fbChat->chat_id = $chat->id;
                $fbChat->ctime = time();
                $fbChat->page_id = $pageId;
                $fbChat->type = $page instanceof erLhcoreClassModelFBPage ? 0 : 1;
                $fbChat->saveOrUpdate();

				if ($botDisabled == 0) {
                    // Set bot
                    erLhcoreClassChatValidator::setBot($chat, array('msg' => $msg));
                    $this->sendBotResponse($chat, $msg, array('init' => true));
                }

                // Create auto responder if there is none
                if ($chat->auto_responder === false) {
                    $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
                    if ($responder instanceof erLhAbstractModelAutoResponder) {
                        $responderChat = new erLhAbstractModelAutoResponderChat();
                        $responderChat->auto_responder_id = $responder->id;
                        $responderChat->chat_id = $chat->id;
                        $responderChat->wait_timeout_send = 1 - $responder->repeat_number;
                        $responderChat->saveThis();

                        $chat->auto_responder_id = $responderChat->id;
                        $chat->auto_responder = $responderChat;
                    }
                }


                if ($chat->auto_responder !== false) {

                    $responder = $chat->auto_responder->auto_responder;

                    $isOnline = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                        'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                        'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                        'exclude_bot' => true
                    ));

                    if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->offline_message != '' && $isOnline === false) {

                        $msgResponder = new erLhcoreClassModelmsg();
                        $msgResponder->msg = trim($responder->offline_message);
                        $msgResponder->chat_id = $chat->id;
                        $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                        $msgResponder->user_id = -2;
                        $msgResponder->time = time() + 1;
                        erLhcoreClassChat::getSession()->save($msgResponder);

                        $chat->last_msg_id = $msgResponder->id;

                    } elseif ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->wait_message != '' && $isOnline === true) {

                        $msgResponder = new erLhcoreClassModelmsg();
                        $msgResponder->msg = trim($responder->wait_message);
                        $msgResponder->chat_id = $chat->id;
                        $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                        $msgResponder->user_id = -2;
                        $msgResponder->time = time() + 1;
                        erLhcoreClassChat::getSession()->save($msgResponder);

                        $chat->last_msg_id = $msgResponder->id;
                    }

                    if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $chat->auto_responder !== false) {
                        if ($chat->auto_responder->active_send_status != 0 && $chat->last_user_msg_time < $chat->last_op_msg_time) {
                            $chat->auto_responder->active_send_status = 0;
                            $chat->auto_responder->saveThis();
                        }
                    }
                }

                $chat->updateThis();

                if (isset($msgResponder)) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                        'chat' => & $chat,
                        'msg' => $msgResponder
                    ));
                }

				/**
				 * Execute standard callback as chat was started
				 */
				erLhcoreClassChatEventDispatcher::getInstance ()->dispatch ( 'chat.chat_started', array (
						'chat' => & $chat,
						'msg' => $msg,
                        'bot_disabled' => $botDisabled == 1
				) );

				$db->commit();

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
			            } else if ($data['type'] == 'audio') {
                            $messageText .= '[url=' . $data['payload']['url'].']Download audio[/url]';
			            } else if ($data['type'] == 'image') {
                            $messageText .= '[img]' . $data['payload']['url'] . '[/img]';
                        } else if ($data['type'] == 'location') {
                            $messageText .= '[url=' . $data['url'].']' . $data['title'] . '[/url] (' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].')[loc]' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].'[/loc]';
			            } else {
			                $messageText .= 'Unknown type - '.json_encode($data);
			            }
			        }
			    }

                $last_user_msg_time = $chat->last_user_msg_time;

				/**
				 * It was standard message
				 */
				if ($eventMessage->getQuickReplyPayload() == '') {
                    $msg = new erLhcoreClassModelmsg ();
                    $msg->msg = trim ( $messageText );
                    $msg->chat_id = $chat->id;
                    $msg->user_id = 0;
                    $msg->time = time ();

                    erLhcoreClassChat::getSession ()->save ( $msg );

                    $chat->last_msg_id = $msg->id;
                    $chat->last_user_msg_time = $msg->time;
                    $chat->saveThis ();

                    $this->sendBotResponse($chat, $msg);

                } else if (strpos($eventMessage->getQuickReplyPayload(), 'trigger__') === 0) {
                    $payloadParts = explode('__',$eventMessage->getQuickReplyPayload());
                    $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                    $this->sendBotResponse($chat, $message, array('type' => 'trigger', 'payload' => $payloadParts[1] . '__' . $payloadParts[2]));
                } else if (strpos($eventMessage->getQuickReplyPayload(), 'bpayload__') === 0) {
                    $payloadParts = explode('__',$eventMessage->getQuickReplyPayload());
                    $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                    $this->sendBotResponse($chat, $message, array('type' => 'payload', 'payload' => $payloadParts[1] . '__' . $payloadParts[2]));
                }

                if ($chat->auto_responder !== false) {

                    $responder = $chat->auto_responder->auto_responder;

                    $isOnline = erLhcoreClassChat::isOnline($chat->dep_id, false, array(
                        'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
                        'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value,
                        'exclude_bot' => true
                    ));

                    if ($chat->status !== erLhcoreClassModelChat::STATUS_BOT_CHAT && is_object($responder) && $responder->offline_message != '' && $isOnline === false) {
                        $msgResponder = new erLhcoreClassModelmsg();
                        $msgResponder->msg = trim($responder->offline_message);
                        $msgResponder->chat_id = $chat->id;
                        $msgResponder->name_support = $responder->operator != '' ? $responder->operator : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Live Support');
                        $msgResponder->user_id = -2;
                        $msgResponder->time = time() + 1;
                        erLhcoreClassChat::getSession()->save($msgResponder);

                        $chat->last_msg_id = $msgResponder->id;
                    }

                    if ($chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $chat->auto_responder !== false) {
                        if ($chat->auto_responder->active_send_status != 0 && $last_user_msg_time < $chat->last_op_msg_time) {
                            $chat->auto_responder->active_send_status = 0;
                            $chat->auto_responder->saveThis();
                        }
                    }
                }

                $chat->updateThis();

                $db->commit();

                if (isset($msgResponder))
                {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                        'chat' => & $chat,
                        'msg' => $msgResponder
                    ));
                }

				erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive',array('chat' => & $chat, 'msg' => & $msg));

				erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_fb',array('chat' => & $chat, 'msg' => & $msg));
				
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
				$fbChat->page_id = $pageId;
                $fbChat->type = $page instanceof erLhcoreClassModelFBPage ? 0 : 1;
				$fbChat->saveOrUpdate();

				$db->commit();				
			} catch (Exception $e) {
				$db->rollback();
				throw $e;
			}
		}
	}

    public function processEchoMessage($eventMessage)
    {
        $messageEcho = $eventMessage->getMessageEcho();
        if ($messageEcho->getAppId() !== null) {
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

        $page = $this->getPage();
        $pageId = $page instanceof erLhcoreClassModelFBPage ? $page->id : $page->page_id;

        $fbChat = erLhcoreClassModelFBChat::findOne ( array (
            'filter' => array (
                'user_id' => $userId,
                'recipient_user_id' => $recipientUserId,
                'page_id' => $pageId,
                'type' => $page instanceof erLhcoreClassModelFBPage ? 0 : 1
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
                    } else if ($data['type'] == 'audio') {
                        $messageText .= '[url=' . $data['payload']['url'].']Download audio[/url]';
                    } else if ($data['type'] == 'image') {
                        $messageText .= '[img]' . $data['payload']['url'].'[/img]';
                    } else if ($data['type'] == 'location') {
                        $messageText .= '[url=' . $data['url'].']' . $data['title'] . '[/url] (' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].')[loc]' . $data['payload']['coordinates']['lat'] . ',' . $data['payload']['coordinates']['long'].'[/loc]';
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
        $page = $this->getPage();
        $botDisabled = $page->bot_disabled;
        $pageId = $page instanceof erLhcoreClassModelFBPage ? $page->id : $page->page_id;

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
                'page_id' => $pageId,
                'type' => $page instanceof erLhcoreClassModelFBPage ? 0 : 1
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

            $fbOptions = erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
            $data = (array)$fbOptions->data;
            if (!isset($data['new_chat']) || $data['new_chat'] == false)
            {
                if (isset($data['priority']) && $data['priority'] != '' && $data['priority'] != 0) {
                    $chat->priority = isset($data['priority']) ? (int)$data['priority'] : 0;
                }

                if ($chat->gbot_id > 0 && isset($chat->chat_variables_array['gbot_disabled']) && $chat->chat_variables_array['gbot_disabled'] == 1 && $botDisabled == 0) {
                    $chatVariables = $chat->chat_variables_array;
                    unset($chatVariables['gbot_disabled']);
                    $chat->chat_variables_array = $chatVariables;
                    $chat->chat_variables = json_encode($chatVariables);
                }

                if ($chat->gbot_id > 0) {
                    $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
                } else {
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                    $chat->user_id = 0; // fix https://github.com/LiveHelperChat/fbmessenger/issues/6
                    $chat->pnd_time = time();
                }

                $chat->saveThis();
            } else {
                $chat = null;
            }
        }

        if (! ($chat instanceof erLhcoreClassModelChat)) {
            try {

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

                if ($page->verified == true)
                {
                    try {
                        $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->page_token);
                        $profile = $messenger->getUserProfile($eventMessage->getSenderId());
                        $dataArray['fb_gender'] = $profile->getGender();
                        $dataArray['fb_locale'] = $profile->getLocale();

                        $lead = erLhcoreClassModelFBLead::findOne(array('filter' => array('user_id' => $eventMessage->getSenderId())));

                        if (!($lead instanceof erLhcoreClassModelFBLead)) {
                            $lead = new erLhcoreClassModelFBLead();
                            $lead->user_id = $eventMessage->getSenderId();
                            $lead->first_name = $profile->getFirstName();
                            $lead->last_name = $profile->getLastName();
                            $lead->profile_pic = $profile->getProfilePic();
                            $lead->locale = $profile->getLocale();
                            $lead->timezone = $profile->getTimezone();
                            $lead->gender = $profile->getGender();
                            $lead->is_payment_enabled = 0;
                            $lead->ctime = time();
                            $lead->page_id = $pageId;
                            $lead->type = $page instanceof erLhcoreClassModelFBPage ? 0 : 1;
                            $lead->dep_id = $department->id;
                            $lead->saveThis();
                        } elseif ($lead->blocked == 1) {
                            $lead->blocked = 0;
                            $lead->saveThis();
                        }

                        if (!isset($data['chat_attr']) || $data['chat_attr'] == 0) {
                            $nick = trim($profile->getFirstName() . ' ' . $profile->getLastName());
                        } else {

                            $additionalDataArray = array();

                            if ($lead->first_name != '') {
                                $additionalDataArray[] = array(
                                    'key' => 'Name',
                                    'identifier' => 'firstname',
                                    'value' => $lead->first_name,
                                );
                            }

                            if ($lead->last_name != '') {
                                $additionalDataArray[] = array(
                                    'key' => 'Last name',
                                    'identifier' => 'lastname',
                                    'value' => $lead->last_name,
                                );
                            }

                            if (!empty($additionalDataArray)) {
                                $chat->additional_data_array = $additionalDataArray;
                                $chat->additional_data = json_encode($additionalDataArray);
                            }
                        }
                    } catch (Exception $e) {
                        erLhcoreClassLog::write($e->getMessage());
                    }
                }

                $chat->nick = $nick;
                $chat->time = time ();
                $chat->status = 0;
                $chat->hash = erLhcoreClassChat::generateHash ();
                $chat->referrer = '';
                $chat->session_referrer = '';
                $chat->chat_variables = json_encode ( $dataArray );
                $chat->pnd_time = time();
                $chat->saveThis ();

                $_SERVER['HTTP_USER_AGENT'] = 'API, Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36';

                $eChat = new erLhcoreClassModelChatIncoming();
                $eChat->incoming_id = $recipientUserId;
                $eChat->chat_external_id = $userId.'_'.$pageId.'_'.($page instanceof erLhcoreClassModelFBPage ? 0 : 1);

                erLhcoreClassChatWebhookIncoming::assignOnlineVisitor($chat, $eChat);

                $msgInitial = new erLhcoreClassModelmsg();
                $msgInitial->msg = "Facebook user started a chat.";
                $msgInitial->chat_id = $chat->id;
                $msgInitial->user_id = -1;
                $msgInitial->time = time ();
                $msgInitial->saveThis();

                $chat->last_msg_id = $msgInitial->id;
                $chat->last_user_msg_time = $msgInitial->time;
                $chat->saveThis ();

                $fbChat->user_id = $userId;
                $fbChat->recipient_user_id = $recipientUserId;
                $fbChat->chat_id = $chat->id;
                $fbChat->ctime = time();
                $fbChat->page_id = $pageId;
                $fbChat->type = $page instanceof erLhcoreClassModelFBPage ? 0 : 1;
                $fbChat->saveOrUpdate();

                $db->commit();

                if ($botDisabled == 0) {

                    $msgBot = clone $msgInitial;
                    $msgBot->msg = $eventMessage->getPostbackPayload();

                    $paramsExecution['msg'] = $msgBot;

                    // Set bot
                    erLhcoreClassChatValidator::setBot($chat, $paramsExecution);

                    $this->sendBotResponse($chat, $msgInitial, array('init' => true));
                }

                /**
                 * Execute standard callback as chat was started
                 */
                erLhcoreClassChatEventDispatcher::getInstance ()->dispatch ( 'chat.chat_started', array (
                    'chat' => & $chat,
                    'msg' => $msgInitial,
                    'fb_user_id' => $userId
                ));

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {

            if ($botDisabled == 0) {
                if ($eventMessage->getPostbackPayload() == 'GET_STARTED') {

                    $msgInitial = new erLhcoreClassModelmsg();
                    $msgInitial->msg = "Facebook user started a chat.";
                    $msgInitial->chat_id = $chat->id;
                    $msgInitial->user_id = -1;
                    $msgInitial->time = time ();
                    $msgInitial->saveThis();

                    $msgInitial->msg = 'GET_STARTED';

                    $this->sendBotResponse($chat, $msgInitial);
                } else { // @todo add regular buttons support
                    if (strpos($eventMessage->getPostbackPayload(), 'trigger__') === 0) {
                        $payloadParts = explode('__',$eventMessage->getPostbackPayload());
                        $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                        $this->sendBotResponse($chat, $message, array('type' => 'trigger', 'payload' => $payloadParts[1] . '__' . $payloadParts[2]));
                    } else if (strpos($eventMessage->getPostbackPayload(), 'bpayload__') === 0) {
                        $payloadParts = explode('__',$eventMessage->getPostbackPayload());
                        $message = erLhcoreClassModelmsg::fetch($payloadParts[3]);
                        $this->sendBotResponse($chat, $message, array('type' => 'payload', 'payload' => $payloadParts[1] . '__' . $payloadParts[2]));
                    }
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_passive', array('chat' => & $chat, 'msg' => & $msg));

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.messages_added_fb', array('chat' => & $chat, 'msg' => & $msg));
            }

        }
    }

    public function sendBotResponse($chat, $msg, $params = array()) {
        if ($chat->gbot_id > 0 && (!isset($chat->chat_variables_array['gbot_disabled']) || $chat->chat_variables_array['gbot_disabled'] == 0)) {

            $chat->refreshThis();

            if (!isset($params['init']) || $params['init'] == false) {
                if (isset($params['type']) && $params['type'] == 'payload' && $msg instanceof erLhcoreClassModelmsg) {
                    erLhcoreClassGenericBotWorkflow::processButtonClick($chat, $msg, $params['payload'], array('processed' => false));
                } else if (isset($params['type']) && $params['type'] == 'trigger' && $msg instanceof erLhcoreClassModelmsg) {
                    erLhcoreClassGenericBotWorkflow::processTriggerClick($chat, $msg, $params['payload'], array('processed' => false));
                } else {
                    erLhcoreClassGenericBotWorkflow::userMessageAdded($chat, $msg);
                }
            }

            // Find a new messages
            $botMessages = erLhcoreClassModelmsg::getList(array('filter' => array('user_id' => -2, 'chat_id' => $chat->id), 'filtergt' => array('id' => $msg->id)));
            foreach ($botMessages as $botMessage) {

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                    'chat' => & $chat,
                    'msg' => $botMessage
                ));
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


