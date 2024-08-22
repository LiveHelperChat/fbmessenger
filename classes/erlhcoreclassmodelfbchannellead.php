<?php

class erLhcoreClassModelFBChannelLead
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'bot_channels_lead';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array (
            'id' => $this->id,
            'channel_id' => $this->channel_id,
            'lead_id' => $this->lead_id
        );
    }

    public function __toString()
    {
        return $this->channel_id;
    }

    public $id = null;

    public $channel_id = 0;

    public $lead_id = 0;
}

?>