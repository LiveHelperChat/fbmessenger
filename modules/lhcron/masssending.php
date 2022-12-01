<?php
/**
 * php cron.php -s site_admin -e fbmessenger -c cron/masssending
 * */

$db = ezcDbInstance::get();

$db->beginTransaction();

try {

    $stmt = $db->prepare('SELECT id FROM lhc_fbmessengerwhatsapp_message WHERE status = :status LIMIT :limit FOR UPDATE ');
    $stmt->bindValue(':limit',60,PDO::PARAM_INT);
    $stmt->bindValue(':status',\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_PENDING_PROCESS, PDO::PARAM_INT);
    $stmt->execute();
    $chatsId = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (Exception $e) {
    // Someone is already processing. So we just ignore and retry later
    return;
}

if (!empty($chatsId)) {

    $instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

    $templatesCache = [];
    $phonesCache = [];

    $mbOptions = \erLhcoreClassModelChatConfig::fetch('fbmessenger_options');
    $data = (array)$mbOptions->data;

    // Delete indexed chat's records
    $stmt = $db->prepare('UPDATE lhc_fbmessengerwhatsapp_message SET status = :status WHERE id IN (' . implode(',', $chatsId) . ')');
    $stmt->bindValue(':status',\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_IN_PROCESS, PDO::PARAM_INT);
    $stmt->execute();
    $db->commit();

    $messages = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getList(['filterin' => ['id' => $chatsId]]);

    if (!empty($messages)) {
        foreach ($messages as $message) {

            if ($message->business_account !== null) {

                $instance->setAccessToken($message->business_account->access_token);
                $instance->setBusinessAccountID($message->business_account->business_account_id);

                $templates = isset($templatesCache[$message->business_account->business_account_id]) ? $templatesCache[$message->business_account->business_account_id] : $instance->getTemplates();
                $phones = isset($phonesCache[$message->business_account->business_account_id]) ? $phonesCache[$message->business_account->business_account_id] : $instance->getPhones();

                $templatesCache[$message->business_account->business_account_id] = $templates;
                $phonesCache[$message->business_account->business_account_id] = $phones;
            } else {

                $instance->setAccessToken($data['whatsapp_access_token']);
                $instance->setBusinessAccountID($data['whatsapp_business_account_id']);

                $templates = isset($templatesCache[0]) ? $templatesCache[0] : $instance->getTemplates();
                $phones = isset($phonesCache[0]) ? $phonesCache[0] : $instance->getPhones();

                $templatesCache[0] = $templates;
                $phonesCache[0] = $phones;
            }

            $instance->sendTemplate($message, $templates, $phones);
        }
    }

} else {
    $db->rollback();
}

?>