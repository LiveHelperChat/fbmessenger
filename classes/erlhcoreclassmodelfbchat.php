<?php

class erLhcoreClassModelFBChat
{
	use erLhcoreClassDBTrait;

	public static $dbTable = 'lhc_fbmessenger_chat';

	public static $dbTableId = 'id';

	public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

	public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
        	'user_id' => $this->user_id,
        	'recipient_user_id' => $this->recipient_user_id,
        	'chat_id' => $this->chat_id,
        	'ctime' => $this->ctime            
        );
    }

    public function __toString()
    {
    	return $this->ctime;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'ctime_front':
                $this->ctime_front = date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                return $this->ctime_front;
                break;

            case 'chat':
            	
            	$this->chat = false;
            	
            	if ($this->chat_id > 0) {
            		$this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
            	}
            	
                return $this->chat;
                break;

            default:
                ;
                break;
        }
    }


    public $id = null;

    public $user_id = null;
    
    public $recipient_user_id = null;

    public $chat_id = null;

    public $ctime = 0;    
}

?>