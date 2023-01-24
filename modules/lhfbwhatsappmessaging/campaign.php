<?php

if ($Params['user_parameters_unordered']['action'] == 'copy' && is_numeric($Params['user_parameters_unordered']['id'])) {

    $campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($Params['user_parameters_unordered']['id']);

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        die('Invalid CSRF Token');
        exit;
    }

    $campaign->id = null;
    $campaign->name = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Copy of').' '.$campaign->name;
    $campaign->enabled = 0;
    $campaign->status = LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_PENDING;
    $campaign->starts_at = 0;
    $campaign->saveThis();

    foreach (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getList(['sort' => 'id ASC', 'limit' => false,'filter' => ['campaign_id' => $Params['user_parameters_unordered']['id']]]) as $copyRecipient) {
        $copyRecipient->id = null;
        $copyRecipient->campaign_id = $campaign->id;
        $copyRecipient->status = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::STATUS_PENDING;
        $copyRecipient->message_id = 0;
        $copyRecipient->conversation_id = 0;
        $copyRecipient->opened_at = 0;
        $copyRecipient->send_at = 0;
        $copyRecipient->log = '';
        $copyRecipient->saveThis();
    }

    exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/campaign.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/campaign.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/campaign.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

if (!$currentUser->hasAccessTo('lhfbwhatsappmessaging','all_campaigns')) {
    $filterParams['filter']['customfilter'][] = ' (private = 0 OR user_id = ' . (int)$currentUser->getUserID() . ')';
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if (!$currentUser->hasAccessTo('lhfbwhatsappmessaging','all_campaigns')) {
    $filterParams['filter']['filter']['user_id'] = $currentUser->getUserID();
}

$pages = new lhPaginator();
$pages->items_total = LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaign').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaign');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index') ,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Facebook chat'),
    ),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Campaign'))
);

?>