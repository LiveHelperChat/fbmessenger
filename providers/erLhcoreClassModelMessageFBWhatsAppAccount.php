<?php

namespace LiveHelperChatExtension\fbmessenger\providers;
#[\AllowDynamicProperties]
class erLhcoreClassModelMessageFBWhatsAppAccount
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessengerwhatsapp_account';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'dep_id' => $this->dep_id,
            'name' => $this->name,
            'access_token' => $this->access_token,
            'business_account_id' => $this->business_account_id,
            'active' => $this->active,
            'phone_number_ids' => $this->phone_number_ids,
            'phone_number_deps' => $this->phone_number_deps
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'department':
                $this->department = null;
                if ($this->dep_id > 0) {
                    try {
                        $this->department = \erLhcoreClassModelDepartament::fetch($this->dep_id,true);
                    } catch (\Exception $e) {

                    }
                }
                return $this->department;

            case 'phone_number_ids_array':
            case 'phone_number_deps_array':
                $attr = str_replace('_array','',$var);
                if (!empty($this->{$attr})) {
                    $jsonData = json_decode($this->{$attr},true);
                    if ($jsonData !== null) {
                        $this->{$var} = $jsonData;
                    } else {
                        $this->{$var} = array();
                    }
                } else {
                    $this->{$var} = array();
                }
                return $this->{$var};

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $dep_id = 0;
    public $active = 1;
    public $name = '';
    public $access_token = '';
    public $business_account_id = '';
    public $phone_number_ids = '';
    public $phone_number_deps = '';
}

?>