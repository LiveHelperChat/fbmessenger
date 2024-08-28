<?php 

return array(
    'enable_debug' => false, // Log errors etc in cache/default.log file
    'fb_disabled' => false, // If you only WhatsApp integration you can disable facebook messenger related part completely
    'standalone' => array (
        'enabled' => false,                            # Is standalone mode enabled
        'secret_hash' => '',                           # Put any random string we use for communication and verifying request
        'address' => 'https://demo.livehelperchat.com' # Master instance where all login happens
    ),
    'app_settings' => array (
        'installation' => 'local',                     # If local we will use switch database SQL query, otherwise we will use HTTP request
        'app_id' => getenv('FBM_APPID') ?: '', # Facebook Messenger App ID
        'app_secret' => getenv('FBM_APPSECRET') ?: '', # Facebook Messenger App Secret
        'verify_token' => getenv('FBM_VERIFYTOKEN') ?: '', # Facebook Messenger Verify Token
        'whatsapp_verify_token' => getenv('WHATSAPP_VERIFYTOKEN') ?: '',
    ),
    'elastic_search' => array(
        'search_attr' => 'attr_int_1'
    )
);

?>
