<?php
class Adabra_Feed_Model_Resource_Feed extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('adabra_feed/feed', 'adabra_feed_id');
    }

}
