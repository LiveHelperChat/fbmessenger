<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/messages.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/messages.php','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'extension/fbmessenger/classes/filter/messages.php','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

erLhcoreClassChatStatistic::formatUserFilter($filterParams, 'lhc_fbmessengerwhatsapp_message');

if (isset($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
    $filterParams['filter']['filterin']['dep_id'] = $filterParams['filter']['filterin']['lh_chat.dep_id'];
    unset($filterParams['filter']['filterin']['lh_chat.dep_id']);
}

if (!$currentUser->hasAccessTo('lhfbwhatsappmessaging','all_send_messages')) {
    $filterParams['filter']['customfilter'][] = ' (private = 0 OR user_id = ' . (int)$currentUser->getUserID() . ')';
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if ($Params['user_parameters_unordered']['export'] == 'csv') {
    \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingValidator::exportMessagesCSV(array_merge($filterParams['filter'], array('limit' => 100000, 'offset' => 0)));
    exit;
}

if ($Params['user_parameters_unordered']['export'] == 'stats') {
    $tpl = erLhcoreClassTemplate::getInstance('lhfbwhatsapp/quickstats.tpl.php');
    if (isset($filterParams['filter']['filterin']['status'])) {
        unset($filterParams['filter']['filterin']['status']);
    }
    $tpl->set('filter', $filterParams['filter']);
    echo $tpl->fetch();
    exit;
}

$rowsNumber = null;

$filterWithoutSort = $filterParams['filter'];
unset($filterWithoutSort['sort']);

if (empty($filterWithoutSort) && method_exists('\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage','estimateRows')) {
    $rowsNumber = ($rowsNumber = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::estimateRows()) && $rowsNumber > 10000 ? $rowsNumber : null;
}

$pages = new lhPaginator();
$pages->items_total = is_numeric($rowsNumber) ? $rowsNumber : \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('fbwhatsapp/messages').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppMessage::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('fbwhatsapp/messages');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('fbmessenger/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger','Facebook chat')),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/fbmessenger', 'Messages')
    )
);

?>