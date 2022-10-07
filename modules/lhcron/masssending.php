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
    $templates = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->getTemplates();
    $phones = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->getPhones();

    // Delete indexed chat's records
    $stmt = $db->prepare('UPDATE lhc_fbmessengerwhatsapp_message SET status = :status WHERE id IN (' . implode(',', $chatsId) . ')');
    $stmt->bindValue(':status',\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::STATUS_IN_PROCESS, PDO::PARAM_INT);
    $stmt->execute();
    $db->commit();

    $messages = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getList(['filterin' => ['id' => $chatsId]]);

    if (!empty($messages)) {
        foreach ($messages as $message) {
            LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->sendTemplate($message, $templates, $phones);
        }
    }

} else {
    $db->rollback();
}

?>