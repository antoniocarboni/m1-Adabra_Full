<?php
class Adabra_Feed_Model_Resource_Vfield extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('adabra_feed/vfield', 'adabra_feed_vfield_id');
    }

}
