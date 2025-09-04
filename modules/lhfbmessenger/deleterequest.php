<?php

$ext = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger');

// Your Facebook App Secret
$app_secret = $ext->settings['app_settings']['app_secret'];

// Function to parse and verify the signed_request
function parse_signed_request($signed_request, $secret) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // Decode data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    // Confirm the signature
    $expected_sig = hash_hmac('sha256', $payload, $secret, true);
    if (hash_equals($expected_sig, $sig)) {
        return $data;
    } else {
        return null;
    }
}

// Helper for decoding
function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

// Get POST data from Facebook
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signed_request = $_POST['signed_request'] ?? '';

    $data = parse_signed_request($signed_request, $app_secret);

    if ($data) {
        // You now have $data['user_id'] (the Facebook user ID)
        $user_id = $data['user_id'];

        foreach (erLhcoreClassModelFBLead::getList(array('filter' => array('user_id' => $user_id))) as $item) {
            $item->removeThis();
        }

        foreach (erLhcoreClassModelFBMessengerUser::getList(array('filter' => array('fb_user_id' => $user_id))) as $item) {
            $item->removeThis();
        }

        foreach (erLhcoreClassModelMyFBPage::getList(array('filter' => array('fb_user_id' => $user_id))) as $item) {
            $item->removeThis();
        }

        // Standalone instance data deletion flow
        if (erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['enabled'] == true) {
            $db = ezcDbInstance::get();

            // Delete page from any other instance in case it was added
            $stmt = $db->prepare('SELECT * FROM `lhc_fbmessenger_standalone_fb_page`');
            $stmt->execute();

            $cfg = erConfigClassLhConfig::getInstance();

            foreach ($stmt->fetchAll() as $row) {

                $db = ezcDbInstance::get();
                $db->query('USE '.$cfg->getSetting( 'db', 'database_user_prefix') . $row['instance_id']);

                foreach (erLhcoreClassModelFBLead::getList(array('filter' => array('user_id' => $user_id))) as $item) {
                    $item->removeThis();
                }

                foreach (erLhcoreClassModelFBMessengerUser::getList(array('filter' => array('fb_user_id' => $user_id))) as $item) {
                    $item->removeThis();
                }

                foreach (erLhcoreClassModelMyFBPage::getList(array('filter' => array('fb_user_id' => $user_id))) as $item) {
                    $item->removeThis();
                }

                $deleteStmt = $db->prepare('DELETE FROM `lhc_fbmessenger_standalone_fb_page` WHERE `fb_user_id` = :fb_user_id');
                $deleteStmt->bindValue(':fb_user_id', $user_id);
                $deleteStmt->execute();
            }
        }

        // Respond with confirmation JSON
        $response = [
            "url" => erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('fbmessenger/deleterequeststatus')  . '/' . $user_id,
            "confirmation_code" => uniqid() // generate unique confirmation
        ];

        header('Content-Type: application/json');
        echo json_encode($response);

    } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid signed request"]);
    }
}
exit;
?>