<?php
/*
 * Register page to proxy from standalone instance
 * */

erLhcoreClassRestAPIHandler::setHeaders();

try {

    $bodyRequest = file_get_contents('php://input');

    $hash = sha1($bodyRequest.'_'.erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionFbmessenger')->settings['standalone']['secret_hash']);

    if ($hash == $Params['user_parameters']['hash']) {
        $requestBody = json_decode($bodyRequest,true);

        if ($requestBody['action'] == 'add') {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare("INSERT IGNORE INTO lhc_fbmessenger_standalone_fb_page (page_id, address) VALUES (:page_id, :address)");
            $stmt->bindValue( ':page_id',$requestBody['page_id']);
            $stmt->bindValue( ':address',$requestBody['address']);
            $stmt->execute();
        } elseif ($requestBody['action'] == 'remove') {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare("DELETE FROM lhc_fbmessenger_standalone_fb_page WHERE page_id = :page_id AND address = :address");
            $stmt->bindValue( ':page_id',$requestBody['page_id']);
            $stmt->bindValue( ':address',$requestBody['address']);
            $stmt->execute();
        }

        echo json_encode(array('success' => true));

    } else {
        throw new Exception('Invalid hash verification!');
    }

} catch (Exception $e) {

    http_response_code(400);

    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}



exit;
?>