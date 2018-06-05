<?php

$page = erLhcoreClassModelMyFBPage::fetch($Params['user_parameters']['page_id']);
$page->bot_disabled = $Params['user_parameters']['status'] == 'true' ? 1 : 0;
$page->saveThis();

echo "ok";
exit;
?>