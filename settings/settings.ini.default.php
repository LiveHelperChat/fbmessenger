<?php 

return array(
    'enable_debug' => false, // Log errors etc in cache/default.log file
    'standalone' => array (
        'enabled' => false,                            # Is standalone mode enabled
        'secret_hash' => '',                           # Put any random string we use for communication and verifying request
        'address' => 'https://demo.livehelperchat.com' # Master instance where all login happens
    ),
    'app_settings' => array (
        'app_id' => getenv('FBM_APPID') ?: '', # Facebook Messenger App ID
        'app_secret' => getenv('FBM_APPSECRET') ?: '', # Facebook Messenger App Secret
        'verify_token' => getenv('FBM_VERIFYTOKEN') ?: '', # Facebook Messenger Verify Token
    ),
    'elastic_search' => array(
        'search_attr' => 'attr_int_1'
    )
);

?>
