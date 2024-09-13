<?php

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $ext->getPage()->verify_token) {
    if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {

        if ($ext->settings['enable_debug'] == true) {
            erLhcoreClassLog::write('VERIFIED');
        }

        echo $_GET['hub_challenge'];
        exit;
    }
}

erLhcoreClassLog::write('Request from app directly - '.date('Y-m-d H:i:s'));

echo "all ok";
exit;
?>