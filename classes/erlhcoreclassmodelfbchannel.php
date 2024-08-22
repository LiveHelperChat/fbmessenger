<?php

class erLhcoreClassModelFBChannel
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'bot_channels';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array (
            'id' => $this->id,
            'name' => $this->name
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {

            default:
                ;
                break;
        }
    }

    public $id = null;

    public $name = null;
}

?>