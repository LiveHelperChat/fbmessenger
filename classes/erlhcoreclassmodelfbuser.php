<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelFBMessengerUser
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_fbuser';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
        	'user_id' => $this->user_id,
        	'fb_user_id' => $this->fb_user_id,
        	'access_token' => $this->access_token           
        );
    }

    private static $fb = null;

    public static function getFBAppInstance() {

        $ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

        return new \Facebook\Facebook([
            'app_id' => $ext->settings['app_settings']['app_id'],
            'app_secret' =>  $ext->settings['app_settings']['app_secret'],
            'default_graph_version' => 'v20.0'
        ]);
    }

    public static function getFBApp($redirect = true)
    {
        if (self::$fb === null) {

            $ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

            self::$fb = new \Facebook\Facebook([
                'app_id' => $ext->settings['app_settings']['app_id'],
                'app_secret' => $ext->settings['app_settings']['app_secret'],
                'default_graph_version' => 'v20.0'
            ]);

            $fbUser = erLhcoreClassModelFBMessengerUser::findOne(array('filter' => array('user_id' => erLhcoreClassUser::instance()->getUserID())));

            if ($fbUser instanceof erLhcoreClassModelFBMessengerUser){
                self::$fb->setDefaultAccessToken($fbUser->access_token);
            } else {
                if ($redirect == true) {
                    $permissions = ['email', 'pages_show_list', 'pages_messaging', 'instagram_manage_messages', 'instagram_basic', 'pages_manage_metadata']; // Optional permissions
                    $helper = self::$fb->getRedirectLoginHelper();
                    header('Location: ' . $helper->getLoginUrl('https://'.$_SERVER['HTTP_HOST']. erLhcoreClassDesign::baseurl('fbmessenger/fbcallback'), $permissions));
                    exit;
                } else {
                    return false;
                }
            }
        }

        return self::$fb;
    }

    public function __toString()
    {
    	return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
                
            case 'callback_url':
                $this->callback_url = erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('fbmessenger/callback') . '/' . $this->id;
                return $this->callback_url;
                break;
                
            default:
                ;
                break;
        }
    }
    
    public $id = null;

    public $user_id = null;
    
    public $fb_user_id = null;

    public $access_token = null;   
    
}

?>