<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$fb = erLhcoreClassModelFBMessengerUser::getFBApp();

use Tgallice\FBMessenger\Messenger;

$item = erLhcoreClassModelMyFBPage::fetch($Params['user_parameters']['id']);

if ($Params['user_parameters_unordered']['action'] == 'addbutton') {
    $messenger = Messenger::create($item->page_token);
    $messenger->setStartedButton('GET_STARTED');
} else if ($Params['user_parameters_unordered']['action'] == 'rembutton') {
    $messenger = Messenger::create($item->page_token);
    $messenger->deleteStartedButton();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;



?>