<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/rendersend.tpl.php');

$params = explode('||',$Params['user_parameters']['template']);

$tpl->setArray([
    'data' => (isset($_POST['data']) ? json_decode($_POST['data'],true) : []),
    'template' => LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance()->getTemplate($params[2], $params[1]),
]);

$response = explode('<!--=========||=========-->', $tpl->fetch());

echo json_encode([
    'preview' => $response[0],
    'form' => $response[1],
]);

exit;

?>