<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsappmessaging/mailingrecipient.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/mailing_recipient.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/mailing_recipient.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

// Mailing list filter
if (!empty($filterParams['input_form']->ml)) {
    $filterParams['filter']['innerjoin']['lhc_fbmessengerwhatsapp_contact_list_contact'] = array('`lhc_fbmessengerwhatsapp_contact`.`id`','`lhc_fbmessengerwhatsapp_contact_list_contact`.`contact_id`');
    $filterParams['filter']['filterin']['`lhc_fbmessengerwhatsapp_contact_list_contact`.`contact_list_id`'] = $filterParams['input_form']->ml;
}

if (!$currentUser->hasAccessTo('lhfbwhatsappmessaging','all_contact_list')) {
    $filterParams['filter']['customfilter'][] = ' (private = 0 OR user_id = ' . (int)$currentUser->getUserID() . ')';
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('fbwhatsappmessaging/mailingrecipient').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppContact::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('fbwhatsappmessaging/mailingrecipient');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array(
        'url' => erLhcoreClassDesign::baseurl('fbmessenger/index') ,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Facebook chat'),
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('fbwhatsappmessaging/mailinglist'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Recipients lists')
    ),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Recipient list'))
);

?>