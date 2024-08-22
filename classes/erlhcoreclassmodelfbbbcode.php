<?php

class erLhcoreClassModelFBBBCode
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_fbmessenger_bbcode';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassExtensionFbmessenger::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'bbcode' => $this->bbcode,
            'configuration' => $this->configuration
        );
    }

    public function __toString()
    {
        return $this->bbcode;
    }

    public function __get($var)
    {
        switch ($var) {

            case 'configuration_array':
                $this->configuration_array = json_decode($this->configuration,true);
                return $this->configuration_array;
                break;
            default:
                ;
                break;
        }
    }

    public $id = null;

    public $name = null;

    public $bbcode = null;

    public $configuration = null;
}

?>