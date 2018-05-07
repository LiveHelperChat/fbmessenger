<?php

class erLhcoreClassModelFBNotificationScheduleItem
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_notification_schedule_item';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'schedule_id' => $this->schedule_id,
            'status' => $this->status,
            'lead_id' => $this->lead_id,
            'campaign_id' => $this->campaign_id,
            'send_time' => $this->send_time,
            'log' => $this->log,
        );
    }

    public function __toString()
    {
        return $this->lead_id;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'dep':
                $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->dep;
                break;

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_ERROR = 2;

    public $id = null;
    public $schedule_id = 0;
    public $status = 0;
    public $lead_id = 0;
    public $campaign_id = 0;
    public $send_time = 0;
    public $log = '';
}

?>