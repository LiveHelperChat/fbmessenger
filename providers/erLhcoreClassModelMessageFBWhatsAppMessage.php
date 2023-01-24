<?php

namespace LiveHelperChatExtension\fbmessenger\providers;

class erLhcoreClassModelMessageFBWhatsAppMessage
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_message';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'phone' => $this->phone,
            'phone_whatsapp' => $this->phone_whatsapp,
            'phone_sender' => $this->phone_sender,
            'phone_sender_id' => $this->phone_sender_id,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'template' => $this->template,
            'template_id' => $this->template_id,
            'language' => $this->language,
            'fb_msg_id' => $this->fb_msg_id,
            'send_status_raw' => $this->send_status_raw,
            'chat_id' => $this->chat_id,
            'dep_id' => $this->dep_id,
            'initiation' => $this->initiation,
            'conversation_id' => $this->conversation_id,
            'message_variables' => $this->message_variables,
            'business_account_id' => $this->business_account_id,
            'scheduled_at' => $this->scheduled_at,
            'campaign_id' => $this->campaign_id,
            'campaign_recipient_id' => $this->campaign_recipient_id,
            'recipient_id' => $this->recipient_id,
            'private' => $this->private
        );
    }

    public function beforeSave($params = array())
    {
        if ($this->created_at == 0) {
            $this->created_at = time();
        }

        $this->updated_at = time();
        $this->phone = str_replace('+','',$this->phone);
    }

    public function __toString()
    {
        return $this->phone;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'can_delete':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsapp','delete_all_messages') || (\erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsapp','delete_messages') && $this->user_id == \erLhcoreClassUser::instance()->getUserID());

            case 'updated_at_ago':
                $this->updated_at_ago = \erLhcoreClassChat::formatSeconds(time() - $this->updated_at);
                return $this->updated_at_ago;

            case 'created_at_front':
                $this->created_at_front = date('Ymd') == date('Ymd',$this->created_at) ? date(\erLhcoreClassModule::$dateHourFormat,$this->created_at) : date(\erLhcoreClassModule::$dateDateHourFormat,$this->created_at);
                return $this->created_at_front;

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = \erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;

            case 'campaign_recipient':
                $this->campaign_recipient = null;
                if ($this->campaign_recipient_id > 0) {
                    $this->campaign_recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::fetch($this->campaign_recipient_id);
                }
                return $this->campaign_recipient;

            case 'department':
                $this->department = null;
                if ($this->dep_id > 0) {
                    try {
                        $this->department = \erLhcoreClassModelDepartament::fetch($this->dep_id,true);
                    } catch (\Exception $e) {

                    }
                }
                return $this->department;

            case 'campaign':
                $this->campaign = null;
                if ($this->campaign_id > 0) {
                    try {
                        $this->campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($this->campaign_id, true);
                    } catch (\Exception $e) {

                    }
                }
                return $this->campaign;

            case 'business_account':
                $this->business_account = null;
                if ($this->business_account_id > 0) {
                    try {
                        $this->business_account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($this->business_account_id);
                    } catch (\Exception $e) {

                    }
                }
                return $this->business_account;

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

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;
    const STATUS_DELIVERED = 2;
    const STATUS_READ = 3;
    const STATUS_PENDING_PROCESS = 4;
    const STATUS_IN_PROCESS = 5;
    const STATUS_FAILED = 6;
    const STATUS_REJECTED = 7;
    const STATUS_SCHEDULED = 8;

    const INIT_US = 0;
    const INIT_THIRD_PARTY = 1;

    const LIST_PUBLIC = 0;
    const LIST_PRIVATE = 1;

    public $id = null;
    public $phone = '';
    public $phone_whatsapp = '';
    public $phone_sender_id = '';
    public $phone_sender = '';
    public $message = '';
    public $created_at = 0;
    public $updated_at = 0;
    public $status = self::STATUS_PENDING;
    public $user_id = 0;
    public $template = '';
    public $template_id = '';
    public $language = '';
    public $fb_msg_id = '';
    public $conversation_id = '';
    public $send_status_raw = '';
    public $message_variables = '';
    public $chat_id = 0;
    public $dep_id = 0;
    public $business_account_id = 0;
    public $scheduled_at = 0;
    public $campaign_id = 0;
    public $recipient_id = 0;
    public $private = self::LIST_PUBLIC;
    public $campaign_recipient_id = 0;
    public $initiation = self::INIT_US;
}

?>