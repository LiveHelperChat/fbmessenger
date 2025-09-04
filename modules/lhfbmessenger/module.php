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

$ViewList['callbackstandaloneinstagram'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['callbackstandalonewhatsapp'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['callbackgeneral'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['deleterequest'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['deleterequeststatus'] = array(
    'params' => array('user_id'),
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

$ViewList['updatedepartment'] = array(
    'params' => array('page_id','dep_id'),
    'uparams' => array(),
    'functions' => array('use_fb_messenger'),
);

$ViewList['updatedepartmentwhatsapp'] = array(
    'params' => array('whatsapp_business_account_id','phone_number_id','dep_id'),
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

$ViewList['deletelead'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['pagesubscribe'] = array(
    'params' => array('id'),
    'uparams' => array('action','dep','csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['whatsappsubscribe'] = array(
    'params' => array('business_id','whatsapp_business_account_id','phone_number_id'),
    'uparams' => array('action','dep'),
    'functions' => array('use_admin'),
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

$ViewList['fbcallbackapp'] = array(
    'params' => array(),
    'uparams' => array(),
);

$ViewList['deletenotification'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('use_fb_messenger'),
);

$ViewList['callbackinstagram'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['callbackwhatsapp'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['callbackstandalonelegacy'] = array(
    'params' => array(),
    'uparams' => array()
);

$FunctionList['use_admin'] = array('explain' => 'Allow operator to use see menu option');
$FunctionList['use_fb_messenger'] = array('explain' => 'Allow operator to use Facebook Messenger/WhatsApp');
$FunctionList['use_options'] = array('explain' => 'Allow operator to configure Facebook Messenger/WhatsApp module');
