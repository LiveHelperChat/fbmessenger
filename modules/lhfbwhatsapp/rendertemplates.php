<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/rendertemplates.tpl.php');

$templates = [];
$phones = [];

if (str_starts_with($Params['user_parameters']['business_account_id'],'whatsapp-')) {
    $phones = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatBusinessValidator::getPhoneNumbers(str_replace('whatsapp-', '', $Params['user_parameters']['business_account_id']));
    $templates = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatBusinessValidator::getTemplates(str_replace('whatsapp-', '', $Params['user_parameters']['business_account_id']));
} else {
    $instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

    if (is_numeric($Params['user_parameters']['business_account_id']) && $Params['user_parameters']['business_account_id'] > 0) {
        $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($Params['user_parameters']['business_account_id']);
        $instance->setAccessToken($account->access_token);
        $instance->setBusinessAccountID($account->business_account_id);
    }

    $templates = $instance->getTemplates();
    $phones = $instance->getPhones();
}

$tpl->setArray([
    'templates' => $templates,
    'phones' => $phones,
]);

$response = explode('<!--=========||=========-->', $tpl->fetch());

echo json_encode([
    'templates' => $response[0],
    'phones' => $response[1]
]);

exit;

?>