<?php

class erLhcoreClassModelFBPage
{
	use erLhcoreClassDBTrait;

	public static $dbTable = 'lhc_fbmessenger_page';

	public static $dbTableId = 'id';

	public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

	public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
        	'dep_id' => $this->dep_id,
        	'page_token' => $this->page_token,
        	'verify_token' => $this->verify_token,
        	'app_secret' => $this->app_secret,
        	'name' => $this->name,
        	'verified' => $this->verified,
            'bot_disabled' => $this->bot_disabled
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
                
            case 'callback_url':
                $this->callback_url = erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('fbmessenger/callback') . '/' . $this->id;
                return $this->callback_url;
                break;
                
            default:
                ;
                break;
        }
    }

    /**
     * Delete page chat's
     */
    public function beforeRemove()
    {
        $q = ezcDbInstance::get()->createDeleteQuery();
        $q->deleteFrom('lhc_fbmessenger_chat')->where($q->expr->eq('page_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
    }
    
    public $id = null;

    public $dep_id = null;
    
    public $page_token = null;

    public $verify_token = null;
    
    public $app_secret = null;

    public $name = '';   
    
    public $verified = 0;

    public $bot_disabled = 0;
}

?>