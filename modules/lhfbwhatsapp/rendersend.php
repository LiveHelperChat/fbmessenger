<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/rendersend.tpl.php');

$params = explode('||',$Params['user_parameters']['template']);

$instance = LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();

if (is_numeric($Params['user_parameters']['business_account_id'])) {
    $account = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::fetch($Params['user_parameters']['business_account_id']);
    $instance->setAccessToken($account->access_token);
    $instance->setBusinessAccountID($account->business_account_id);
}

$tpl->setArray([
    'data' => (isset($_POST['data']) ? json_decode($_POST['data'],true) : []),
    'template' => $instance->getTemplate($params[2], $params[1]),
]);

$response = explode('<!--=========||=========-->', $tpl->fetch());

echo json_encode([
    'preview' => $response[0],
    'form' => $response[1],
]);

exit;

?>