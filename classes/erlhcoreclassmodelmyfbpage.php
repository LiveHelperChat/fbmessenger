<?php

class erLhcoreClassModelMyFBPage
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_my_page';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'page_id' => $this->page_id,
            'access_token' => $this->access_token,
            'enabled' => $this->enabled,
            'dep_id' => $this->dep_id,
            'bot_disabled' => $this->bot_disabled,
        );
    }

    public function afterSave()
    {
        
        if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == true) {
            erLhcoreClassFBValidator::processSubscribeOnMaster([
                'page_id' => $this->page_id,
                'address' => $_SERVER['HTTP_HOST'],
                'action' => 'add'
            ]);
        }
        
        /*$cfg = erConfigClassLhConfig::getInstance();

        $db = ezcDbInstance::get();
        $db->query('USE '.$cfg->getSetting( 'db', 'database'));
        $stmt = $db->prepare("INSERT IGNORE INTO lhc_instance_fb_page (page_id, instance_id) VALUES (:page_id, :instance_id)");
        $stmt->bindValue( ':page_id',$this->page_id);
        $stmt->bindValue( ':instance_id',erLhcoreClassInstance::getInstance()->id);
        $stmt->execute();

        $db->query('USE '.$cfg->getSetting( 'db', 'database_user_prefix').erLhcoreClassInstance::getInstance()->id);*/
    }

    public function afterRemove()
    {
        if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == true) {
            erLhcoreClassFBValidator::processSubscribeOnMaster([
                'page_id' => $this->page_id,
                'address' => $_SERVER['HTTP_HOST'],
                'action' => 'remove'
            ]);
        }

        /*$cfg = erConfigClassLhConfig::getInstance();

        $db = ezcDbInstance::get();
        $db->query('USE '.$cfg->getSetting( 'db', 'database'));
        $stmt = $db->prepare("DELETE FROM lhc_instance_fb_page WHERE page_id = :page_id AND instance_id = :instance_id");
        $stmt->bindValue( ':page_id',$this->page_id);
        $stmt->bindValue( ':instance_id',erLhcoreClassInstance::getInstance()->id);
        $stmt->execute();

        $db->query('USE '.$cfg->getSetting( 'db', 'database_user_prefix').erLhcoreClassInstance::getInstance()->id);*/
    }

    public function __get($var)
    {
        switch ($var) {

            case 'verified':
                    return true;
                break;

            case 'page_token':
                    return $this->access_token;
                break;

            default:
                ;
                break;
        }
    }

    public $id = null;

    public $page_id = null;

    public $access_token = null;

    public $enabled = null;

    public $bot_disabled = 0;
}

?>