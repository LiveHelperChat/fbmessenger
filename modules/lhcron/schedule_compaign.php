<?php

/**
 * php cron.php -s site_admin -e fbmessenger -c cron/schedule_compaign
 * */

$schedule = erLhcoreClassModelFBNotificationSchedule::findOne(array('filtergt' => array('start_at' => 0), 'filterlt' => array('start_at' => time()), 'filter' => array('status' => erLhcoreClassModelFBNotificationSchedule::STATUS_PENDING)));

if ($schedule instanceof erLhcoreClassModelFBNotificationSchedule) {

    $db = ezcDbInstance::get();

    $db->beginTransaction() ;

    $schedule->syncAndLock();
    $schedule->status = erLhcoreClassModelFBNotificationSchedule::STATUS_PROCESSED;
    $schedule->saveThis();

    $db->commit();

    $scheduleCampaign = new erLhcoreClassModelFBNotificationScheduleCampaign();
    $scheduleCampaign->schedule_id = $schedule->id;
    $scheduleCampaign->ctime = time();
    $scheduleCampaign->saveThis();

    echo "Schedule compaign - " . $scheduleCampaign->id,"\n";
} else {
    echo "No schedules were found!","\n";
}

?>