<?php

$Module = array( "name" => "FB WhatsApp module" );

$ViewList = array();

$ViewList['massmessage'] = array(
    'params' => array(),
    'uparams' => array('business_account_id'),
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
    'params' => array(),
    'uparams' => array('recipient','business_account_id'),
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

$ViewList['rendertemplates'] = array(
    'params' => array('business_account_id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['messages'] = array(
    'params' => array(),
    'uparams' => array(
        'phone','phone_sender','status_ids','user_ids',
        'timefrom','timefrom_seconds','timefrom_minutes','timefrom_hours',
        'timeto', 'timeto_minutes', 'timeto_seconds', 'timeto_hours',
        'campaign_ids','template_ids','business_account_ids','department_ids','export'
        ),
    'functions' => array('use_admin'),
    'multiple_arguments' => array(
        'user_ids',
        'campaign_ids',
        'template_ids',
        'status_ids',
        'business_account_ids',
        'department_ids'
    )
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

// Messages window related
$FunctionList['all_send_messages'] = array('explain' => 'Operator can see all send messages, otherwise only his own and public');
$FunctionList['delete_messages'] = array('explain' => 'Allow operator to delete sent messages, only his own');
$FunctionList['delete_all_messages'] = array('explain' => 'Allow operator to delete all sent messages, not only his own');