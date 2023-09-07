<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelFBNotificationSchedule
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_notification_schedule';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'message' => $this->message,
            'filter' => $this->filter,
            'start_at' => $this->start_at,
            'status' => $this->status,
            'last_send' => $this->last_send,
            'interval' => $this->interval,
            'amount' => $this->amount
        );
    }

    public function __toString()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'dep':
                $this->dep = erLhcoreClassModelDepartament::fetch($this->dep_id);
                return $this->dep;
                break;

            case 'filter_array':
                $this->filter_array = array();
                if ($this->filter != '')
                {
                    $jsonData = json_decode($this->filter,true);
                    if ($jsonData !== null) {
                        $this->filter_array = $jsonData;
                    }
                }
                return $this->filter_array;
                break;

            case 'start_at_day':
                    if ($this->start_at > 0) {
                        $this->start_at_day = date('Y-m-d',$this->start_at);
                    } else {
                       $this->start_at_day = '';
                    }

                    return $this->start_at_day;
                break;

            case 'start_at_hour':
                    if ($this->start_at > 0) {
                        $this->start_at_hour = date('H',$this->start_at);
                    } else {
                        $this->start_at_hour = '';
                    }

                return $this->start_at_hour;
                break;

            case 'start_at_minute':
                    if ($this->start_at > 0) {
                        $this->start_at_minute = date('i',$this->start_at);
                    } else {
                        $this->start_at_minute = '';
                    }

                return $this->start_at_minute;
                break;

            default:
                ;
                break;
        }
    }

    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;

    public $id = null;
    public $name = '';
    public $filter = '';
    public $start_at = 0;
    public $status = 0;
    public $last_send = 0;
    public $interval = 60;
    public $amount = 10;
    public $message = '';

}

?>