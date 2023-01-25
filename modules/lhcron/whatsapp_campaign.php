<?php
/**
 * php cron.php -s site_admin -e fbmessenger -c cron/whatsapp_campaign
 *
 * Run every 1 minute or so.
 *
 * */

// Pending campaigns to start
$campaignValid = \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::getList(['filternot' => ['status' => \LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppCampaign::STATUS_FINISHED], 'filterlt' => ['starts_at' => time()], 'filter' => ['enabled' => 1]]);

$cfg = erConfigClassLhConfig::getInstance();
$worker = $cfg->getSetting( 'webhooks', 'worker' );

foreach ($campaignValid as $campaign) {
    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        if (erLhcoreClassRedis::instance()->llen('resque:queue:lhc_fbwhatsapp_campaign') <= 4) {
            erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_fbwhatsapp_campaign', '\LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingWorker', array('campaign_id' => $campaign->id));
        }
    } else {
        $worker = (new \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppMailingWorker());
        $worker->args['campaign_id'] = $campaign->id;
        $worker->perform();
    }
}




?>