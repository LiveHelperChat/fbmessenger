<?php

namespace LiveHelperChatExtension\fbmessenger\providers;
#[\AllowDynamicProperties]
class erLhcoreClassModelMessageFBWhatsAppCampaign
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_campaign';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'dep_id' => $this->dep_id,
            'status' => $this->status,
            'starts_at' => $this->starts_at,
            'enabled' => $this->enabled,
            'business_account_id' => $this->business_account_id,
            'message_variables' => $this->message_variables,
            'phone_sender' => $this->phone_sender,
            'phone_sender_id' => $this->phone_sender_id,
            'template' => $this->template,
            'template_id' => $this->template_id,
            'language' => $this->language,
            'private' => $this->private,
        );
    }

    public function afterRemove()
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_fbmessengerwhatsapp_campaign_recipient` WHERE `campaign_id` = :id');
        $stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(\erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(\erLhcoreClassModule::$dateDateHourFormat, $this->mtime);

            case 'can_edit':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','edit_all_campaign') || $this->user_id == \erLhcoreClassUser::instance()->getUserID();

            case 'can_delete':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_all_campaign') || (\erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_campaign') && $this->user_id == \erLhcoreClassUser::instance()->getUserID());

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = \erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;

            case 'business_account':
                $this->business_account = null;
                if ($this->business_account_id > 0) {
                    $this->business_account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($this->business_account_id);
                }
                return $this->business_account;

            case 'business_account_front':
                $this->business_account_front = (string)$this->business_account;
                return $this->business_account_front;

            case 'message_variables_array':
                if (!empty($this->message_variables)) {
                    $jsonData = json_decode($this->message_variables,true);
                    if ($jsonData !== null) {
                        $this->message_variables_array = $jsonData;
                    } else {
                        $this->message_variables_array = $this->message_variables;
                    }
                } else {
                    $this->message_variables_array = array();
                }
                return $this->message_variables_array;

            case 'total_contacts':
                return \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getCount(['filter' => ['campaign_id' => $this->id]]);

            default:
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;

    const LIST_PUBLIC = 0;
    const LIST_PRIVATE = 1;

    public $id = NULL;
    public $name = '';
    public $user_id = 0;
    public $enabled = 0;
    public $status = self::STATUS_PENDING;
    public $starts_at = 0;
    public $dep_id = 0;
    public $business_account_id = 0;
    public $message_variables = '';
    public $private = self::LIST_PUBLIC;

    public $phone_sender = '';
    public $phone_sender_id = '';
    public $template = '';
    public $template_id = '';
    public $language = '';
}

?>