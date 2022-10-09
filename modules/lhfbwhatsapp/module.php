<?php

$Module = array( "name" => "FB WhatsApp module" );

$ViewList = array();

$ViewList['massmessage'] = array(
    'params' => array(),
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
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['templates'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['rendersend'] = array(
    'params' => array('template'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['messages'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$FunctionList['use_admin'] = array('explain' => 'Allow operator to use WhatsApp');
