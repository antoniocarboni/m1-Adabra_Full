<?php
class Adabra_Feed_Model_Resource_Feed_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('adabra_feed/feed');
    }

}
