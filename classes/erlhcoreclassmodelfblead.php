<?php

class erLhcoreClassModelFBLead
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_lead';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'location' => $this->location,
            'profile_pic' => $this->profile_pic,
            'is_payment_enabled' => $this->is_payment_enabled,
            'ctime' => $this->ctime,
            'page_id' => $this->page_id,
            'type' => $this->type,
            'dep_id' => $this->dep_id
        );
    }

    public function __toString()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'dep':
                    $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                    return $this->dep;
                break;

            default:
                ;
                break;
        }
    }

    public $id = null;

    public $user_id = 0;

    public $first_name = '';

    public $last_name = '';

    public $locale = '';

    public $profile_pic = '';

    public $gender = '';

    public $timezone = 0;

    public $ctime = 0;

    public $location = '';

    public $email = '';

    public $phone = '';

    public $country = '';

    public $page_id = 0;

    public $type = 0;

    public $dep_id = 0;
}

?>