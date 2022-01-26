<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$item =  erLhcoreClassModelFBPage::fetch($Params['user_parameters']['id']);

use Tgallice\FBMessenger\Messenger;

if ($Params['user_parameters_unordered']['action'] == 'addbutton') {
    $messenger = Messenger::create($item->page_token);
    $messenger->setStartedButton('GET_STARTED');
} else if ($Params['user_parameters_unordered']['action'] == 'rembutton') {
    $messenger = Messenger::create($item->page_token);
    $messenger->deleteStartedButton();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

exit;
?>