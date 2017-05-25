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
	}
	
	public function sendMessageToFb($params)
	{
    	$chatVariables = $params['chat']->chat_variables_array;
    	
    	if (isset($chatVariables['fb_chat']) && $chatVariables['fb_chat'] == 1 && $this->settings['pages_messaging_enabled'] == true)
    	{
    	    try {
        	    $messenger = Tgallice\FBMessenger\Messenger::create($this->settings['page_token']);
        	    
        	    $response = $messenger->sendMessage($chatVariables['fb_user_id'], $params['msg']->msg);
        	    
        	    
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
				        'recipient_user_id' => $recipientUserId
				) 
		) );
		
		$db = ezcDbInstance::get();
		
		if (!($fbChat instanceof erLhcoreClassModelFBChat)) {
			$fbChat = new erLhcoreClassModelFBChat();
		}
				
		$chat = $fbChat->chat;
		
		$needSave = false;
				
		if (! ($chat instanceof erLhcoreClassModelChat)) {
			try {
				$needSave = true;
				
				$db->beginTransaction();
			
				$chat = new erLhcoreClassModelChat ();
				
				// Set default department
				$departments = erLhcoreClassModelDepartament::getList ( array (
						'limit' => 1,
						'filter' => array (
								'disabled' => 0 
						) 
				) );
				
				if (! empty ( $departments )) {
					$department = array_shift ( $departments );
					$chat->dep_id = $department->id;
					$chat->priority = $department->priority;
				} else {
					throw new Exception ( 'Could not detect default department' );
				}
				
				$dataArray = array (
						'fb_chat' => true,
						'fb_user_id' => $userId
				);
				
				$nick = 'FB Visitor - ' . $userId;
				
				if ($this->settings['pages_messaging_enabled'] == true)
				{
    				$messenger = Tgallice\FBMessenger\Messenger::create($this->settings['page_token']);				
    				$profile = $messenger->getUserProfile($event->getSenderId());
    				$dataArray['fb_gender'] = $profile->getGender();
    				$dataArray['fb_locale'] = $profile->getLocale();
    				$nick = trim($profile->getFirstName() . ' ' . $profile->getLastName());
				}
				
				$chat->nick = $nick;
				$chat->time = time ();
				$chat->status = 0;
				$chat->hash = erLhcoreClassChat::generateHash ();
				$chat->referrer = '';
				$chat->session_referrer = '';
				$chat->chat_variables = json_encode ( $dataArray );
				
				$chat->saveThis ();
				
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
				$fbChat->ctime = getRecipientId;
				$fbChat->saveOrUpdate();

				$db->commit();				
			} catch (Exception $e) {
				$db->rollback();
				throw $e;
			}
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
				'erLhcoreClassModelFBChat' => 'extension/fbmessenger/classes/erlhcoreclassmodelfbchat.php' 
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
	
	private static $persistentSession;
	
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


