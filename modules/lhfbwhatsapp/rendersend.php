<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/rendersend.tpl.php');

$params = explode('||',$Params['user_parameters']['template']);

if (str_starts_with($Params['user_parameters']['business_account_id'],'whatsapp-')) {
    $template = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChatBusinessValidator::getTemplate($params[2], $params[1]);
} else {
    $instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

    if (is_numeric($Params['user_parameters']['business_account_id']) && $Params['user_parameters']['business_account_id'] > 0) {
        $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($Params['user_parameters']['business_account_id']);
        $instance->setAccessToken($account->access_token);
        $instance->setBusinessAccountID($account->business_account_id);
    }

    $template = $instance->getTemplate($params[2], $params[1]);
}

$tpl->setArray([
    'data' => (isset($_POST['data']) ? json_decode($_POST['data'],true) : []),
    'template' => $template,
]);

$response = explode('<!--=========||=========-->', $tpl->fetch());

echo json_encode([
    'preview' => $response[0],
    'form' => $response[1],
]);

exit;

?>