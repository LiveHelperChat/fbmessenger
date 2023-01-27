<?php

namespace LiveHelperChatExtension\fbmessenger\providers;

class erLhcoreClassModelMessageFBWhatsAppContactList
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_contact_list';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'private' => $this->private,
            'created_at' => $this->created_at
        );
    }

    public function afterRemove()
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_mailing_list_recipient` WHERE `mailing_list_id` = :ml_id');
        $stmt->bindValue(':ml_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function afterSave($params = array())
    {

        $sql = "SELECT count(id) FROM lhc_fbmessengerwhatsapp_contact_list WHERE id IN 
                                                                 (SELECT contact_list_id FROM lhc_fbmessengerwhatsapp_contact_list_contact WHERE contact_id IN (SELECT contact_id FROM lhc_fbmessengerwhatsapp_contact_list_contact WHERE contact_list_id = :contact_list_id)) 
                                                             AND private = 0";
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':contact_list_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();

        $totalFound = $stmt->fetchColumn();

        $sql = "UPDATE lhc_fbmessengerwhatsapp_contact SET private = :private WHERE id IN (SELECT contact_id FROM lhc_fbmessengerwhatsapp_contact_list_contact WHERE contact_list_id = :contact_list_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':contact_list_id', $this->id, \PDO::PARAM_INT);
        $stmt->bindValue(':private', $totalFound == 0 ? 1 : 0, \PDO::PARAM_INT);

        $stmt->execute();

    }

    public function __toString()
    {
        return $this->mail;
    }

    public function beforeSave($params = array())
    {
        if ($this->created_at == 0) {
            $this->created_at = time();
        }
    }

    public function __get($var)
    {
        switch ($var) {

            case 'can_edit':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','edit_all_contacts') || $this->user_id == \erLhcoreClassUser::instance()->getUserID();

            case 'can_delete':
                return \erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_all_contacts') || (\erLhcoreClassUser::instance()->hasAccessTo('lhfbwhatsappmessaging','delete_contacts') && $this->user_id == \erLhcoreClassUser::instance()->getUserID());

            case 'created_at_front':
                return date('Ymd') == date('Ymd', $this->created_at) ? date(\erLhcoreClassModule::$dateHourFormat, $this->created_at) : date(\erLhcoreClassModule::$dateDateHourFormat, $this->created_at);

            case 'total_contacts':
                return \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactListContact::getCount(['filter' => ['contact_list_id' => $this->id]]);

            case 'user':
                $this->user = null;
                if ($this->user_id > 0) {
                    $this->user = \erLhcoreClassModelUser::fetch($this->user_id);
                }
                return $this->user;
                break;

            default:
                break;
        }
    }

    const LIST_PUBLIC = 0;
    const LIST_PRIVATE = 1;

    public $id = NULL;
    public $name = '';
    public $user_id = 0;
    public $created_at = 0;
    public $private = self::LIST_PUBLIC;
}

?>