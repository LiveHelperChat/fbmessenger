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

            // Delete page from any other instance in case it was added
            $stmt = $db->prepare('DELETE FROM `lhc_fbmessenger_standalone_fb_page` WHERE `page_id` = :page_id AND `instance_id` != :instance_id');
            $stmt->bindValue( ':page_id',$requestBody['page_id']);
            $stmt->bindValue( ':instance_id',$requestBody['instance_id']);
            $stmt->execute();

            $stmt = $db->prepare("INSERT IGNORE INTO lhc_fbmessenger_standalone_fb_page (page_id, address, instance_id, instagram_business_account, whatsapp_business_account_id, whatsapp_business_phone_number_id) VALUES (:page_id, :address, :instance_id, :instagram_business_account, :whatsapp_business_account_id, :whatsapp_business_phone_number_id)");
            $stmt->bindValue( ':page_id',$requestBody['page_id']);
            $stmt->bindValue( ':address',$requestBody['address']);
            $stmt->bindValue( ':instance_id',$requestBody['instance_id']);
            $stmt->bindValue( ':instagram_business_account',$requestBody['instagram_business_account']);
            $stmt->bindValue( ':whatsapp_business_account_id',$requestBody['whatsapp_business_account_id']);
            $stmt->bindValue( ':whatsapp_business_phone_number_id',$requestBody['whatsapp_business_phone_number_id']);
            $stmt->execute();
        } elseif ($requestBody['action'] == 'remove') {
            $db = ezcDbInstance::get();
            $stmt = $db->prepare("DELETE FROM lhc_fbmessenger_standalone_fb_page WHERE page_id = :page_id AND address = :address AND instance_id = :instance_id AND instagram_business_account = :instagram_business_account AND whatsapp_business_account_id = :whatsapp_business_account_id AND whatsapp_business_phone_number_id = :whatsapp_business_phone_number_id");
            $stmt->bindValue( ':page_id',$requestBody['page_id']);
            $stmt->bindValue( ':address',$requestBody['address']);
            $stmt->bindValue( ':instance_id',$requestBody['instance_id']);
            $stmt->bindValue( ':instagram_business_account',$requestBody['instagram_business_account']);
            $stmt->bindValue( ':whatsapp_business_account_id',$requestBody['whatsapp_business_account_id']);
            $stmt->bindValue( ':whatsapp_business_phone_number_id',$requestBody['whatsapp_business_phone_number_id']);
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