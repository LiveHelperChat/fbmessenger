<?php

$Module = array( "name" => "Mailing module");

$ViewList = array();

$ViewList['mailinglist'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['mailingrecipient'] = array(
    'params' => array(),
    'uparams' => array('ml','name','phone','user_ids','delivery_status'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml','user_ids')
);

$ViewList['campaign'] = array(
    'params' => array(),
    'uparams' => array('id','action'),
    'functions' => array( 'use_admin' )
);

$ViewList['newcampaign'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newcampaignrecipient'] = array(
    'params' => array('id','recipient_id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['campaignrecipient'] = array(
    'params' => array(),
    'uparams' => array('campaign','export','status','opened'),
    'functions' => array( 'use_admin' )
);

$ViewList['deleterecipient'] = array(
    'params' => array('id'),
    'uparams' => array('csfr','ml'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml')
);

$ViewList['newmailingrecipient'] = array(
    'params' => array(),
    'uparams' => array('ml'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml')
);

$ViewList['editmailingrecipient'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['newmailinglist'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['editmailinglist'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['editcampaign'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['importfrommailinglist'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletemailinglist'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletecampaign'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletecampaignrecipient'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['detailssend'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['sendtestmessage'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['import'] = array(
    'params' => array(),
    'uparams' => array('ml'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml')
);

$ViewList['importcampaign'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use campaign/contact module');

// Contacts related
$FunctionList['all_contact_list'] = array('explain' => 'Operator can see all contact list, otherwise only his own and public');
$FunctionList['edit_all_contacts'] = array('explain' => 'Allow operator to edit all contacts, not only his own');
$FunctionList['delete_contacts'] = array('explain' => 'Allow operator to delete his own contacts');
$FunctionList['delete_all_contacts'] = array('explain' => 'Allow operator to delete all contacts, not only his own');

// Campaigns
$FunctionList['all_campaigns'] = array('explain' => 'Operator can see all campaigns, otherwise only his own and public');
$FunctionList['delete_campaign'] = array('explain' => 'Allow operator to delete his campaign');
$FunctionList['delete_all_campaign'] = array('explain' => 'Allow operator to delete all campaigns, not only his own');
$FunctionList['edit_all_campaign'] = array('explain' => 'Allow operator to edit all campaigns, not only his own');

?>