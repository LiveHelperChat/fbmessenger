<?php

$Module = array( "name" => "FB Messenger",
				 'variable_params' => true );

$ViewList = array();

$ViewList['callback'] = array(
    'params' => array(),
    'uparams' => array()
);

$FunctionList['use'] = array('explain' => 'Allow operator to use SugarCRM module');
$FunctionList['configure'] = array('explain' => 'Allow operator to configure SugarCRM module');