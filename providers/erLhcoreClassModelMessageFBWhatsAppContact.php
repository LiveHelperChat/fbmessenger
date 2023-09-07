<?php

namespace LiveHelperChatExtension\fbmessenger\providers;
#[\AllowDynamicProperties]
class erLhcoreClassModelMessageFBWhatsAppContact
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_contact';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'data' => $this->data,
            'email' => $this->email,
            'disabled' => $this->disabled,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_recipient' => $this->phone_recipient,

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

            'attr_str_1' => $this->attr_str_1,
            'attr_str_2' => $this->attr_str_2,
            'attr_str_3' => $this->attr_str_3,
            'attr_str_4' => $this->attr_str_4,
            'attr_str_5' => $this->attr_str_5,
            'attr_str_6' => $this->attr_str_6,
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id, // Operator who craeted a contact
            'private' => $this->private,
        );
    }

    public function removeAssignment(){
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_fbmessengerwhatsapp_contact_list_contact` WHERE `contact_id` = :ml_id');
        $stmt->bindValue(':ml_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function beforeSave($params = array())
    {
        if ($this->created_at == 0) {
            $this->created_at = time();
        }
    }

    public function afterRemove()
    {
        $this->removeAssignment();

        // Remove old assignment as recipient is removed
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_fbmessengerwhatsapp_campaign_recipient` WHERE `recipient_id` = :ml_id');
        $stmt->bindValue(':ml_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function afterSave()
    {
        $this->removeAssignment();

        $db = \ezcDbInstance::get();

        if (isset($this->ml_ids) && !empty($this->ml_ids)) {
            $values = [];
            foreach ($this->ml_ids as $ml_id) {
                $values[] = "(" . $this->id . "," . $ml_id . ")";
            }
            if (!empty($values)) {
                $db->query('INSERT INTO `lhc_fbmessengerwhatsapp_contact_list_contact` (`contact_id`,`contact_list_id`) VALUES ' . implode(',', $values));
            }
        }
    }

    public function __toString()
    {
        return $this->phone;
    }

    public function isAllPrivateListMember() {
        $isPrivate = true;

        $contactListItems = $this->ml;

        if (empty($contactListItems)) {
            $isPrivate = false;
        } else {
            foreach ($contactListItems as $contactList) {
                if (
                    $contactList->contact_list->private == \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::LIST_PUBLIC
                ) {
                    $isPrivate = false;
                    break;
                }
            }
        }

        return $isPrivate;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(\erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(\erLhcoreClassModule::$dateDateHourFormat, $this->mtime);

            case 'created_at_front':
                return date('Ymd') == date('Ymd', $this->created_at) ? date(\erLhcoreClassModule::$dateHourFormat, $this->created_at) : date(\erLhcoreClassModule::$dateDateHourFormat, $this->created_at);

            case 'date_front':
                return date(\erLhcoreClassModule::$dateDateHourFormat, $this->date);

            case 'can_edit':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','edit_all_contacts') || $this->user_id == \erLhcoreClassUser::instance()->getUserID();

            case 'can_delete':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_all_contacts') || (\erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_contacts') && $this->user_id == \erLhcoreClassUser::instance()->getUserID());

            case 'ml':
                $this->ml = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactListContact::getList(['filter' => ['contact_id' => $this->id]]);
                return $this->ml;

            case 'ml_ids_front':
                $this->ml_ids_front = [];
                foreach ($this->ml as $ml) {
                    $this->ml_ids_front[] = $ml->contact_list_id;
                }
                return $this->ml_ids_front;

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = \erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;

            default:
                break;
        }
    }

    const DELIVERY_STATUS_UNKNOWN = 0;
    const DELIVERY_STATUS_UNSUBSCRIBED = 1;
    const DELIVERY_STATUS_FAILED = 2;
    const DELIVERY_STATUS_ACTIVE = 3;

    const LIST_PUBLIC = 0;
    const LIST_PRIVATE = 1;

    public $id = NULL;
    public $data = '';
    public $email = '';
    public $disabled = 0;
    public $ml_ids = [];

    public $created_at = 0;
    public $title = '';
    public $lastname = '';
    public $company = '';
    public $date = 0;
    public $delivery_status = self::DELIVERY_STATUS_UNKNOWN;
    public $file_1 = '';
    public $file_2 = '';
    public $file_3 = '';
    public $file_4 = '';

    public $name = '';
    public $phone = '';
    public $phone_recipient = '';
    public $attr_str_1 = '';
    public $attr_str_2 = '';
    public $attr_str_3 = '';
    public $attr_str_4 = '';
    public $attr_str_5 = '';
    public $attr_str_6 = '';
    public $chat_id = 0;
    public $user_id = 0;
    public $private = self::LIST_PUBLIC;

}

?>