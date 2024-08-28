<?php

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

if (isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $ext->settings['app_settings']['verify_token']) {
    if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {

        if ($ext->settings['enable_debug'] == true) {
            erLhcoreClassLog::write('VERIFIED');
        }

        echo $_GET['hub_challenge'];
        exit;
    }
}

$cfg = erConfigClassLhConfig::getInstance();
$db = ezcDbInstance::get();

$dummyPayload = $payloadData = json_decode(file_get_contents("php://input"),true);
/*$dummyPayload = $payloadData = json_decode('{
    "object": "page",
    "entry": [
        {
            "time": 1724842684571,
            "id": "384264185009325",
            "messaging": [
                {
                    "sender": {
                        "id": "384264185009325"
                    },
                    "recipient": {
                        "id": "1524304520946032"
                    },
                    "timestamp": 1724842684321,
                    "message": {
                        "mid": "m_5P-1H4N_iAOMvtJ2qHPhNwfERpjjsOU-gzKWAIcVKXLXvyx-o-BSjXAeeLfR_AA2vbpbdyWrd2rPexo0Isp8vg",
                        "is_echo": true,
                        "text": "Unknown message",
                        "app_id": 1022400286051981
                    }
                }
            ]
        }
    ]
}',true);*/


/*$dummyPayload = $payloadData = json_decode('{
    "object": "page",
    "entry": [
        {
            "time": 1724840886017,
            "id": "384264185009325",
            "messaging": [
                {
                    "sender": {
                        "id": "1524304520946032"
                    },
                    "recipient": {
                        "id": "384264185009325"
                    },
                    "timestamp": 1724840885531,
                    "message": {
                        "mid": "m_RxUCPJ-oZsRXdelvLIMANwfERpjjsOU-gzKWAIcVKXL0cNzRdXolsKCfkje5-jA4ZbBxtblc9qHFNIv4X8QMXA",
                        "text": "Visitor reply"
                    }
                }
            ]
        }
    ]
}',true);*/


/*$dummyPayload = $payloadData = json_decode('',true);
// As page admin
{
    "object": "page",
    "entry": [
        {
            "time": 1724763443734,
            "id": "384264185009325",
            "messaging": [
                {
                    "sender": {
                        "id": "384264185009325"
                    },
                    "recipient": {
                        "id": "1524304520946032"
                    },
                    "timestamp": 1724763443399,
                    "message": {
                        "mid": "m_EwKpDUeVAzjk7Xib56rIhgfERpjjsOU-gzKWAIcVKXLs18Esd1e2ExJRSRXwx0VUsXyTj9Nic3JZ_81MQeBdbA",
                        "is_echo": true,
                        "text": "hello, world",
                        "app_id": 1022400286051981
                    }
                }
            ]
        }
    ]
}

// As visitor
{
    "object": "page",
    "entry": [
        {
            "time": 1724764207808,
            "id": "384264185009325",
            "messaging": [
                {
                    "sender": {
                        "id": "1524304520946032"
                    },
                    "recipient": {
                        "id": "384264185009325"
                    },
                    "timestamp": 1724764207266,
                    "message": {
                        "mid": "m_j5cN5mrIwxzotSjfOVyWjAfERpjjsOU-gzKWAIcVKXLJbEEDeBA0eqN-kKrd_oGuEkMzOqIgK9V8d7PoWTDTmA",
                        "text": "I send now"
                    }
                }
            ]
        }
    ]
}
*/

erLhcoreClassLog::write(print_r($dummyPayload,true));


if (isset($payloadData['entry']) && is_array($payloadData['entry'])) {
    foreach ($payloadData['entry'] as $entryData) {
        $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookmessengerappscope')));
        if (!is_object($webhookPresent)) {
            // Install dependencies with chosen department
            $subscribeNumber = erLhcoreClassModelMyFBPage::findOne(['filter' => ['page_id' => $entryData['id']]]);
            if (is_object($subscribeNumber)){
                \LiveHelperChatExtension\fbmessenger\providers\FBMessengerMessengerAppLiveHelperChatActivator::installOrUpdate(['dep_id' => $subscribeNumber->dep_id]);
                $webhookPresent = erLhcoreClassModelChatIncomingWebhook::findOne(array('filter' => array('scope' => 'facebookmessengerappscope')));
            }
        }
        $identifier = $webhookPresent->identifier;
        break;
    }
}

$Params['user_parameters']['identifier'] = $identifier;
include 'modules/lhwebhooks/incoming.php';

?>