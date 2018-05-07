<?php


/**
 * php cron.php -s site_admin -e fbmessenger -c cron/send_notification
 * */
$scheduleCampaigns = erLhcoreClassModelFBNotificationScheduleCampaign::getList(array('filter' => array('status' => erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTED)));

foreach ($scheduleCampaigns as $campaign) {
    $db = ezcDbInstance::get();

    $db->beginTransaction() ;
    $campaign->syncAndLock();
    if ($campaign->status != erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTED) {
        break;
    } else {
        $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_SENDING;
        $campaign->saveThis();
    }
    $db->commit();

    $schedule = erLhcoreClassModelFBNotificationSchedule::fetch($campaign->schedule_id);

    $itemsToSend = array();

    if ($campaign->last_send < (time()-$schedule->interval) ) {

        $items = erLhcoreClassModelFBNotificationScheduleItem::getList(array('limit' => $schedule->amount, 'filter' => array('status' => erLhcoreClassModelFBNotificationScheduleItem::STATUS_PENDING, 'campaign_id' => $campaign->id)));

        if (!empty($items)) {

            foreach ($items as $item) {
                $item->status = erLhcoreClassModelFBNotificationScheduleItem::STATUS_PROCESSED;
                try {
                    erLhcoreClassFBValidator::sendNotification(array(
                        'item' => & $item,
                        'campaign' => $campaign,
                        'schedule' => $schedule,
                    ));
                } catch (Exception $e) {
                    $item->log = $e->getMessage();
                    $item->status = erLhcoreClassModelFBNotificationScheduleItem::STATUS_ERROR;
                }
                $item->send_time = time();
                $item->saveThis();
            }

            if (count($items) == $schedule->amount) {
                $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTED;
            } else {
                $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_SEND;
            }

        } else {
            $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_SEND;
        }

        $campaign->last_send = time();
        $campaign->saveThis();

    } else {
        echo "Timeout has not passed for campaign  - " . $campaign->id . "\n";
        $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTED;
        $campaign->saveThis();
    }

}

?>