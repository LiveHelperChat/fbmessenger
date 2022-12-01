<?php

$Module = array( "name" => "FB WhatsApp module" );

$ViewList = array();

$ViewList['massmessage'] = array(
    'params' => array('business_account_id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['deletemessage'] = array(
    'params' => array('id'),
    'functions' => array('use_admin'),
);

$ViewList['rawjson'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['send'] = array(
    'params' => array('business_account_id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['templates'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['rendersend'] = array(
    'params' => array('template', 'business_account_id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['messages'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['account'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('manage_accounts'),
);

$ViewList['newaccount'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('manage_accounts'),
);

$ViewList['editaccount'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('manage_accounts'),
);

$ViewList['deleteaccount'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('manage_accounts'),
);

$FunctionList['use_admin'] = array('explain' => 'Allow operator to use WhatsApp');
$FunctionList['manage_accounts'] = array('explain' => 'Manage business accounts');
