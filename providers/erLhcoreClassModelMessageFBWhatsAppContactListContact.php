<?php

namespace LiveHelperChatExtension\fbmessenger\providers;

class erLhcoreClassModelMessageFBWhatsAppContactListContact
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_contact_list_contact';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'contact_list_id' => $this->contact_list_id,
            'contact_id' => $this->contact_id
        );
    }

    public function __toString()
    {
        return $this->email;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mtime_front':
                return date('Ymd') == date('Ymd', $this->mtime) ? date(\erLhcoreClassModule::$dateHourFormat, $this->mtime) : date(\erLhcoreClassModule::$dateDateHourFormat, $this->mtime);

            case 'mailing_recipient':
                $this->mailing_recipient = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::fetch($this->contact_id);
                return $this->mailing_recipient;

            case 'contact_list':
                $this->contact_list = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContactList::fetch($this->contact_list_id);
                return $this->contact_list;

            default:
                break;
        }
    }

    public $id = NULL;
    public $contact_list_id = null;
    public $contact_id = null;
}

?>