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
	
	public function sendMessageToFb($params)
	{
    	$chatVariables = $params['chat']->chat_variables_array;
    	
    	if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1)
    	{
    	    try {    	        
    	        $chat = erLhcoreClassModelFBChat::findOne(array('filter' => array('chat_id' => $params['chat']->id)));
    	        
    	        $this->setPage($chat->page);
    	        
    	        if ($this->getPage()->verified == 1) {
            	    $messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->page_token);   
            	                	    
            	    $messages = self::parseMessageForFB($params['msg']->msg);
            	    
            	    foreach ($messages as $msg) {
            	        if ($msg !== null) {
            	            $response = $messenger->sendMessage($chat->user_id, $msg);
            	        }
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

	public function processVisitorMessage($eventMessage) {
		
	    // User ID
		$userId = $eventMessage->getSenderId ();
		
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
		    $chat->user_id = 0; // fix https://github.com/LiveHelperChat/fbmessenger/issues/6
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

				$initMessage = false;

				if ($this->getPage()->verified == true)
				{				
				    try {   
        				$messenger = Tgallice\FBMessenger\Messenger::create($this->getPage()->page_token);				
        				$profile = $messenger->getUserProfile($eventMessage->getSenderId());
        				$dataArray['fb_gender'] = $profile->getGender();
        				$dataArray['fb_locale'] = $profile->getLocale();
        				$nick = trim($profile->getFirstName() . ' ' . $profile->getLastName()); 	
        				
        				$initMessage = true;
        				
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
				
				$chat->saveThis ();
				
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
				        } else if ($data['type'] == 'image') {
				            $messageText .= '[img]' . $data['payload']['url'].'[/img]';
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
				
				/**
				 * Execute standard callback as chat was started
				 */
				erLhcoreClassChatEventDispatcher::getInstance ()->dispatch ( 'chat.chat_started', array (
						'chat' => & $chat,
						'msg' => $msg 
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
			            } else if ($data['type'] == 'image') {
			                $messageText .= '[img]' . $data['payload']['url'].'[/img]';
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
				'erLhcoreClassFBValidator'  => 'extension/fbmessenger/classes/erlhcoreclassfbvalidator.php'
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


