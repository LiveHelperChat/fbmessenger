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
    'functions' => array('use_fb_messenger'),
);

$ViewList['thread'] = array(
    'params' => array('id'),
    'uparams' => array('action','csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['threadmy'] = array(
    'params' => array('id'),
    'uparams' => array('action','csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['options'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_options')
);

$ViewList['sendtestmessage'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger')
);

$ViewList['notifications'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger')
);

$ViewList['editnotification'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_fb_messenger')
);

$ViewList['newnotification'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger')
);

$ViewList['fblogout'] = array(
    'params' => array(),
    'uparams' => array('csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['myfbpages'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['disablebot'] = array(
    'params' => array('page_id','status'),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['fbcallback'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
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
    'functions' => array('use_fb_messenger'),
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['new'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['deletenotification'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['deletebbcode'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['bbcode'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['newbbcode'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['editbbcode'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$FunctionList['use_admin'] = array('explain' => 'Allow operator to use see menu option');
$FunctionList['use_fb_messenger'] = array('explain' => 'Allow operator to use Facebook Messenger/WhatsApp');
$FunctionList['use_options'] = array('explain' => 'Allow operator to configure Facebook Messenger/WhatsApp module');
