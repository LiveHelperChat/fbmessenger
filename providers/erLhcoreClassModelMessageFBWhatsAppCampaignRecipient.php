<?php

namespace LiveHelperChatExtension\fbmessenger\providers;

class erLhcoreClassModelMessageFBWhatsAppCampaignRecipient
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_campaign_recipient';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'recipient_id' => $this->recipient_id,
            'type' => $this->type,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_recipient' => $this->phone_recipient,
            'status' => $this->status,
            'send_at' => $this->send_at,
            'opened_at' => $this->opened_at,
            'name' => $this->name,
            'attr_str_1' => $this->attr_str_1,
            'attr_str_2' => $this->attr_str_2,
            'attr_str_3' => $this->attr_str_3,
            'attr_str_4' => $this->attr_str_4,
            'attr_str_5' => $this->attr_str_5,
            'attr_str_6' => $this->attr_str_6,
            'message_id' => $this->message_id,
            'conversation_id' => $this->conversation_id,
            'log' => $this->log,
            'created_at' => $this->created_at,
            'title' => $this->title,
            'lastname' => $this->lastname,
            'company' => $this->company,
            'date' => $this->date,
            'delivery_status' => $this->delivery_status, // Last delivery status
            'file_1' => $this->file_1, // File from lhc file list
            'file_2' => $this->file_2, // File from lhc file list
            'file_3' => $this->file_3, // File from lhc file list
            'file_4' => $this->file_4, // File from lhc file list
        );
    }

    public function beforeSave($params = array())
    {
        if ($this->created_at == 0) {
            $this->created_at = time();
        }
    }

    public function __toString()
    {
        return $this->phone;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'send_at_front':
            case 'opened_at_front':
                $varObj = str_replace('_front','',$var);
                $this->$var = date('Ymd') == date('Ymd', $this->{$varObj}) ? date(\erLhcoreClassModule::$dateHourFormat, $this->{$varObj}) : date(\erLhcoreClassModule::$dateFormat, $this->{$varObj});
                return $this->$var;

            case 'campaign':
                $this->campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($this->campaign_id);
                return $this->campaign;

            case 'can_edit':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','edit_all_campaign') || $this->campaign->user_id == \erLhcoreClassUser::instance()->getUserID();

            case 'can_delete':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_all_campaign') || (\erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_campaign') && $this->campaign->user_id == \erLhcoreClassUser::instance()->getUserID());

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = \erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;

            case 'file_1_url':
            case 'file_2_url':
            case 'file_3_url':
            case 'file_4_url':
                $this->{$var} = '';
                $varInternal = str_replace('_url','',$var);
                if ($this->type == self::TYPE_MANUAL) {
                    $this->{$var} = $this->{$varInternal};
                } else {
                    $recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->recipient_id);
                    if ($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        $this->{$var} = $recipient->{$varInternal};
                    }
                }

                if (strpos($this->{$var},'[file=') !== false) {
                    $this->{$var} = \erLhcoreClassBBCodePlain::make_clickable($this->{$var});
                }

                return $this->{$var};

            case 'name_front':
            case 'title_front':
            case 'lastname_front':
            case 'company_front':
            case 'attr_str_1_front':
            case 'attr_str_2_front':
            case 'attr_str_3_front':
            case 'attr_str_4_front':
            case 'attr_str_5_front':
            case 'attr_str_6_front':

            case 'file_1_url_front':
            case 'file_2_url_front':
            case 'file_3_url_front':
            case 'file_4_url_front':

            case 'email_front':
                $this->{$var} = '';
                $varInternal = str_replace('_front','',$var);
                if ($this->type == self::TYPE_MANUAL || in_array($varInternal,[
                        'file_1_url',
                        'file_2_url',
                        'file_3_url',
                        'file_3_url'])) {
                    $this->{$var} = $this->{$varInternal};
                } else {
                    $recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->recipient_id);
                    if ($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        $this->{$var} = $recipient->{$varInternal};
                    }
                }
                return $this->{$var};

            case 'recipient':
                $this->recipient = '';
                if ($this->type == self::TYPE_MANUAL) {
                    $this->recipient = $this->email;
                } else {
                    $recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->recipient_id);
                    if ($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        $this->recipient = $recipient->email;
                    }
                }
                return $this->recipient;

            case 'recipient_phone':
                $this->recipient_phone = '';
                if ($this->type == self::TYPE_MANUAL) {
                    $this->recipient_phone = $this->phone;
                } else {
                    $recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->recipient_id);
                    if ($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        $this->recipient_phone = $recipient->phone;
                    }
                }
                return $this->recipient_phone;

            case 'recipient_phone_recipient':
                $this->recipient_phone_recipient = '';
                if ($this->type == self::TYPE_MANUAL) {
                    $this->recipient_phone_recipient = $this->phone_recipient != '' ? $this->phone_recipient : $this->phone;
                } else {
                    $recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->recipient_id);
                    if ($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        $this->recipient_phone_recipient = $recipient->phone_recipient != '' ? $recipient->phone_recipient : $recipient->phone;
                    }
                }
                return $this->recipient_phone_recipient;

            case 'recipient_attr_str_1':
            case 'recipient_attr_str_2':
            case 'recipient_attr_str_3':
            case 'recipient_attr_str_4':
            case 'recipient_attr_str_5':
            case 'recipient_attr_str_6':
            case 'recipient_name':
                $this->{$var} = '';
                $systemAttr = str_replace('recipient_','',$var);
                if ($this->type == self::TYPE_MANUAL) {
                    $this->{$var} = $this->{$systemAttr};
                } else {
                    $recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->recipient_id);
                    if ($recipient instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact) {
                        $this->{$var} = $recipient->{$systemAttr};
                    }
                }
                return $this->{$var};

            default:
                break;
        }
    }

    const TYPE_MAILING_LIST = 0;
    const TYPE_MANUAL = 1;

    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;
    const STATUS_DELIVERED = 2;
    const STATUS_READ = 3;
    const STATUS_PENDING_PROCESS = 4;
    const STATUS_IN_PROCESS = 5;
    const STATUS_FAILED = 6;
    const STATUS_REJECTED = 7;
    const STATUS_SCHEDULED = 8;

    public $id = NULL;
    public $campaign_id = 0;
    public $recipient_id = 0;
    public $send_at = 0;
    public $type = self::TYPE_MAILING_LIST;
    public $email = '';
    public $phone = '';
    public $phone_recipient = '';
    public $status = self::STATUS_PENDING;
    public $log = '';
    public $message_id = 0;
    public $conversation_id = 0;
    public $opened_at = 0;

    public $created_at = 0;
    public $title = '';
    public $lastname = '';
    public $company = '';
    public $date = 0;
    public $delivery_status = 0;
    public $file_1 = '';
    public $file_2 = '';
    public $file_3 = '';
    public $file_4 = '';

    public $name = '';
    public $attr_str_1 = '';
    public $attr_str_2 = '';
    public $attr_str_3 = '';
    public $attr_str_4 = '';
    public $attr_str_5 = '';
    public $attr_str_6 = '';
}

?>