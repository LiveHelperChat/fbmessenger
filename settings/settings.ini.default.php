<?php 

return array(
    'enable_debug' => false, // Log errors etc in cache/default.log file
    'fb_disabled' => false, // If you only WhatsApp integration you can disable facebook messenger related part completely
    'scopes' => array(
        'email',
        'pages_show_list',
        'pages_messaging',
        'instagram_manage_messages',
        'instagram_basic',
        'pages_manage_metadata',

        // New after update
        'pages_read_engagement',

        // WhatsApp
        'whatsapp_business_management',
        'whatsapp_business_messaging',
        'business_management'
    ),
    'standalone' => array (
        'enabled' => false,                            # Is standalone mode enabled
        'secret_hash' => '',                           # Put any random string we use for communication and verifying request
        'address' => 'https://demo.livehelperchat.com' # Master instance where all login happens
    ),
    'app_settings' => array (
        'app_id' => getenv('FBM_APPID') ?: '', # Facebook Messenger App ID
        'app_secret' => getenv('FBM_APPSECRET') ?: '', # Facebook Messenger App Secret
        'verify_token' => getenv('FBM_VERIFYTOKEN') ?: '', # Facebook Messenger Verify Token
        'whatsapp_verify_token' => getenv('WHATSAPP_VERIFYTOKEN') ?: '',
    )
);

?>
