<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/campaignrecipient.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/campaign_recipient.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/campaign_recipient.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$campaign = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::fetch($filterParams['input_form']->campaign);

if (!($campaign instanceof \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign)) {
    die('Invalid campaign!');
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if ($Params['user_parameters_unordered']['export'] == 'csv') {
    \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::exportCampaignRecipientCSV(array_merge($filterParams['filter'], array('limit' => 100000, 'offset' => 0)), ['campaign' => $campaign]);
    exit;
}

$pages = new lhPaginator();
$pages->items_total = LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaignrecipient').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaignRecipient::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaignrecipient') . '/' . $filterParams['input_form']->campaign;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('campaign', $campaign);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index') ,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat'),
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('fbwhatsappmessaging/campaign'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Campaigns')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('fbwhatsappmessaging/editcampaign') . '/' . $campaign->id,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Edit campaign')
    ),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Recipients'))
);

?>