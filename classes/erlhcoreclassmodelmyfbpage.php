<?php
#[\AllowDynamicProperties]
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
            'instagram_business_account' => $this->instagram_business_account,
            'whatsapp_business_account_id' => $this->whatsapp_business_account_id,
            'whatsapp_business_phone_number_id' => $this->whatsapp_business_phone_number_id,
            'fb_user_id' => $this->fb_user_id,
        );
    }

    public function afterSave()
    {
        if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == true) {
            erLhcoreClassFBValidator::processSubscribeOnMaster([
                'page_id' => $this->page_id,
                'instance_id' => erLhcoreClassInstance::getInstance()->id,
                'instagram_business_account' => $this->instagram_business_account,
                'whatsapp_business_account_id' => $this->whatsapp_business_account_id,
                'whatsapp_business_phone_number_id' => $this->whatsapp_business_phone_number_id,
                'fb_user_id' => $this->fb_user_id,
                'address' => $_SERVER['HTTP_HOST'],
                'action' => 'add'
            ]);
        }
    }

    public function afterRemove()
    {
        if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == true) {
            erLhcoreClassFBValidator::processSubscribeOnMaster([
                'page_id' => $this->page_id,
                'instagram_business_account' => $this->instagram_business_account,
                'whatsapp_business_account_id' => $this->whatsapp_business_account_id,
                'whatsapp_business_phone_number_id' => $this->whatsapp_business_phone_number_id,
                'fb_user_id' => $this->fb_user_id,
                'instance_id' => erLhcoreClassInstance::getInstance()->id,
                'address' => $_SERVER['HTTP_HOST'],
                'action' => 'remove'
            ]);
        }
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

    public $fb_user_id = 0;
    public $instagram_business_account = 0;
    public $whatsapp_business_account_id = 0;
    public $whatsapp_business_phone_number_id = 0;
}

?>