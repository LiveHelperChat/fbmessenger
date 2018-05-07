<?php

/**
 * php cron.php -s site_admin -e fbmessenger -c cron/collect_recipients
 * */
$scheduleCampaigns = erLhcoreClassModelFBNotificationScheduleCampaign::getList(array('filter' => array('status' => erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_PENDING)));

foreach ($scheduleCampaigns as $campaign) {
    $db = ezcDbInstance::get();

    $db->beginTransaction() ;
    $campaign->syncAndLock();
    if ($campaign->status != erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_PENDING) {
        break;
    } else {
        $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTING;
        $campaign->saveThis();
    }
    $db->commit();

    $schedule = erLhcoreClassModelFBNotificationSchedule::fetch($campaign->schedule_id);

    $filter = $schedule->filter_array;
    $filter['blocked'] = 0;

    $pageLimit = 50;

    for ($i = 0; $i < 1000000; $i++) {
        $leads = erLhcoreClassModelFBLead::getList(array('filter' => $filter, 'offset' => 0, 'filtergt' => array('id' => $campaign->last_id), 'limit' => $pageLimit, 'sort' => 'id ASC'));

        if (!empty($leads))
        {
            end($leads);
            $lastLead = current($leads);

            $campaign->last_id = $lastLead->id;
            $campaign->saveThis();

            foreach ($leads as $lead) {
                $db = ezcDbInstance::get();
                $stmt = $db->prepare("INSERT INTO lhc_fbmessenger_notification_schedule_item (lead_id,status,log,schedule_id,campaign_id,send_time) VALUES (:lead_id, 0, '', :schedule_id, :campaign_id, 0)");
                $stmt->bindValue( ':lead_id',$lead->id);
                $stmt->bindValue( ':schedule_id',$campaign->schedule_id);
                $stmt->bindValue( ':campaign_id',$campaign->id);
                $stmt->execute();
            }

        } else {
            $campaign->status = erLhcoreClassModelFBNotificationScheduleCampaign::STATUS_COLLECTED;
            $campaign->saveThis();
            break;
        }
    }
}

?>