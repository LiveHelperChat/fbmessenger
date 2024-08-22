<?php
#[\AllowDynamicProperties]
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
            'blocked' => $this->blocked,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'location' => $this->location,
            'profile_pic' => $this->profile_pic,
            '_wait' => $this->_wait,
            '_quick_save' => $this->_quick_save,
            'linked_account' => $this->linked_account,
            'subscribe' => $this->subscribe,
            'is_payment_enabled' => $this->is_payment_enabled,
            'ctime' => $this->ctime,
            'auto_stop' => $this->auto_stop,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'page_id' => $this->page_id,
            'type' => $this->type,
            'dep_id' => $this->dep_id,
            'profile_pic_updated' => $this->profile_pic_updated
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

            case 'page':
                    if ($this->type == 1) {
                        $this->page = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('page_id' => $this->page_id)));
                    } else {
                        $this->page = erLhcoreClassModelFBPage::fetch($this->page_id);
                    }
                    return $this->page;
                break;

            case 'ctime_front':
                $this->ctime_front = date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                return $this->ctime_front;
                break;

            case 'chat':

                $this->chat = false;

                if ($this->chat_id > 0) {
                    $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                }

                return $this->chat;
                break;

            case 'subscribe_channels':

                if ($this->subscribe != '') {

                    $channels = erLhcoreClassModelFBChannel::getList(array('filterin' => array('id' => explode(',', $this->subscribe))));

                    $names = array();

                    foreach ($channels as $channel) {
                        $names[] = $channel->name;
                    }

                    return implode(', ',$names);
                }
                return $this->subscribe;
                break;


            case 'profile_pic_front':
                $this->profile_pic_front = $this->profile_pic;
                if ($this->profile_pic_updated < time()-5*24*3600) {
                    if ($this->type == 1) {
                        $page = erLhcoreClassModelMyFBPage::findOne(array('filter' => array('page_id' => $this->page_id)));
                        if ($page instanceof erLhcoreClassModelMyFBPage) {
                            $pageToken = $page->page_token;
                        } else {
                            $pageToken = false;
                        }
                    } else {
                        $page = erLhcoreClassModelFBPage::fetch($this->page_id);
                        if ($page instanceof erLhcoreClassModelFBPage) {
                            $pageToken = $page->page_token;
                        } else {
                            $pageToken = false;
                        }
                    }

                    if ($pageToken !== false) {
                        try {
                            $messenger = Tgallice\FBMessenger\Messenger::create($pageToken);
                            $profile = $messenger->getUserProfile($this->user_id);

                            $this->profile_pic_front = $this->profile_pic = $profile->getProfilePic();
                            $this->profile_pic_updated = time();
                            $this->saveThis();
                        } catch (Exception $e) {
                            $this->profile_pic = '';
                            $this->profile_pic_updated = time();
                            $this->saveThis();
                        }
                    }
                }
                return $this->profile_pic_front;
                break;

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $first_name = null;
    public $last_name = null;
    public $profile_pic = null;
    public $locale = null;
    public $gender = null;
    public $email = null;
    public $phone = null;
    public $country = null;
    public $location = null;
    public $timezone = null;
    public $_wait = null;
    public $_quick_save = null;
    public $linked_account = null;
    public $subscribe = null;
    public $is_payment_enabled = null;
    public $auto_stop = null;
    public $created_at = null;
    public $updated_at = null;
    public $deleted_at = null;
    public $user_id = null;
    public $page_id = 0;
    public $profile_pic_updated = 0;
}

?>