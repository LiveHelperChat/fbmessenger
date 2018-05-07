<?php

class erLhcoreClassModelFBNotificationScheduleCampaign
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_notification_schedule_campaign';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'schedule_id' => $this->schedule_id,
            'status' => $this->status,
            'last_id' => $this->last_id,
            'ctime' => $this->ctime,
            'last_send' => $this->last_send,
        );
    }

    public function __toString()
    {
        return $this->schedule_id;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'dep':
                $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->dep;
                break;

            case 'ctime_front':
                $this->ctime_front = date('Ymd') == date('Ymd',$this->ctime) ? date(erLhcoreClassModule::$dateHourFormat,$this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat,$this->ctime);
                return $this->ctime_front;
                break;

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_COLLECTING = 1;
    const STATUS_COLLECTED = 2;
    const STATUS_SENDING = 3;
    const STATUS_SEND = 4;

    public $id = null;
    public $schedule_id = 0;
    public $status = 0;
    public $last_id = 0;
    public $ctime = 0;
    public $last_send = 0;

}

?>