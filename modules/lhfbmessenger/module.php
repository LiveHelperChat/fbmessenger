<?php

$Module = array( "name" => "FB Messenger",
				 'variable_params' => true );

$ViewList = array();

$ViewList['callback'] = array(
    'params' => array('id'),
    'uparams' => array()
);

$ViewList['callbackstandalone'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['callbackgeneral'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['registersubscribe'] = array(
    'params' => array('hash'),
    'uparams' => array()
);

$ViewList['leads'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['thread'] = array(
    'params' => array('id'),
    'uparams' => array('action','csfr'),
    'functions' => array('use_admin'),
);

$ViewList['threadmy'] = array(
    'params' => array('id'),
    'uparams' => array('action','csfr'),
    'functions' => array('use_admin'),
);

$ViewList['options'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin')
);

$ViewList['sendtestmessage'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin')
);

$ViewList['notifications'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin')
);

$ViewList['editnotification'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_admin')
);

$ViewList['newnotification'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin')
);

$ViewList['fblogout'] = array(
    'params' => array(),
    'uparams' => array('csfr'),
    'functions' => array('use_admin'),
);

$ViewList['myfbpages'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['disablebot'] = array(
    'params' => array('page_id','status'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['fbcallback'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['fbcallbackinstance'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array(),
);

$ViewList['fbcallbackstandalone'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array(),
);

$ViewList['fblogininstance'] = array(
    'params' => array('id','uid','time','hash'),
    'uparams' => array(),
    'functions' => array(),
);

$ViewList['registerstandalone'] = array(
    'params' => array('hash'),
    'uparams' => array(),
    'functions' => array(),
);

$ViewList['fbloginstandalone'] = array(
    'params' => array('id','uid','time','hash'),
    'uparams' => array(),
    'functions' => array(),
);

$ViewList['index'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['pagesubscribe'] = array(
    'params' => array('id'),
    'uparams' => array('action','dep','csfr'),
    'functions' => array('use_admin'),
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['new'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_admin'),
);

$ViewList['deletenotification'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_admin'),
);

$ViewList['deletebbcode'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_admin'),
);

$ViewList['bbcode'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['newbbcode'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$ViewList['editbbcode'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_admin'),
);

$FunctionList['use_admin'] = array('explain' => 'Allow operator to configure Facebook Messenger');
