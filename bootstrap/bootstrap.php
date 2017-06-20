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
            	    $response = $messenger->sendMessage($chat->user_id, $params['msg']->msg);
    	        }
        	    
    	    } catch (Exception $e) {
    	        
    	        if ($this->settings['enable_debug'] == true) {
    	            erLhcoreClassLog::write(print_r($e->getMessage(),true))."\n";
    	        }
    	    }
    	}    	
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

				if ($this->settings['pages_messaging_enabled'] == true)
				{				
				    try {   
        				$messenger = Tgallice\FBMessenger\Messenger::create($this->settings['page_token']);				
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
				
				/**
				 * Store new message
				 */
				$msg = new erLhcoreClassModelmsg ();
				$msg->msg = trim ( $eventMessage->getMessageText () );
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
				
				/**
				 * Store new message
				 */
				$msg = new erLhcoreClassModelmsg ();
				$msg->msg = trim ( $eventMessage->getMessageText () );
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
				'erLhcoreClassModelFBChat' => 'extension/fbmessenger/classes/erlhcoreclassmodelfbchat.php',
				'erLhcoreClassModelFBPage' => 'extension/fbmessenger/classes/erlhcoreclassmodelfbpage.php',
				'erLhcoreClassFBValidator' => 'extension/fbmessenger/classes/erlhcoreclassfbvalidator.php' 
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
	
	private static $persistentSession;
	
	private $fbpage = null;
	
	public function __get($var) {
		switch ($var) {
			case 'is_active' :
				return true;
				;
				break;
			
			case 'settings' :
				$this->settings = include ('extension/fbmessenger/settings/settings.ini.php');
				if ($this->settings ['ahosting'] == true) {
					/*
					 * $autoamtedHostingSettings = erLhcoreClassInstance::getInstance()->getCustomFieldsData(1);
					 * $this->settings['is_enabled'] = isset($autoamtedHostingSettings['clicktocall_supported']) && $autoamtedHostingSettings['clicktocall_supported'] == 1;
					 * $this->settings['buttonid'] = isset($autoamtedHostingSettings['clicktocall_buttonid']) ? $autoamtedHostingSettings['clicktocall_buttonid'] : '';
					 * $this->settings['customfields'] = isset($autoamtedHostingSettings['clicktocall_customfields']) ? $autoamtedHostingSettings['clicktocall_customfields'] : '';
					 */
				}
				return $this->settings;
				break;
			
			default :
				;
				break;
		}
	}
}


