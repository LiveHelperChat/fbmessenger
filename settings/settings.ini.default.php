<?php 

return array(
    'enable_debug' => false,    // Log errors etc in cache/default.log file
    'fb_disabled' => false,     // If you only WhatsApp integration you can disable facebook messenger related part completely
    'hide_fb_login' => false,    // Hide fb login option and leave only manual page definition
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
        'disable_manual_whatsapp' => false,            # Should we hide WhatsApp manual numbers section?
        'enabled' => false,                            # Is standalone mode enabled. Are we in automated hosting environment?
        'secret_hash' => '',                           # Put any random string we use for communication and verifying request
        'address' => 'https://master.example.com'      # Master instance where all logins happen
    ),
    'app_settings' => array (
        'app_id' => getenv('FBM_APPID') ?: '', # Facebook Messenger App ID
        'app_secret' => getenv('FBM_APPSECRET') ?: '',                      # Facebook Messenger App Secret
        'verify_token' => getenv('FBM_VERIFYTOKEN') ?: '',                  # Facebook Messenger Verify Token
        'whatsapp_verify_token' => getenv('WHATSAPP_VERIFYTOKEN') ?: '',    # WhatsApp Verify Token
        'instagram_verify_token' => getenv('INSTAGRAM_VERIFYTOKEN') ?: '',  # Instagram verify Token
    )
);

?>
