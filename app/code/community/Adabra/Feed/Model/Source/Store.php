<?php
class Adabra_Feed_Model_Source_Store extends Adabra_Feed_Model_Source_Abstract
{
    public function toOptionArray()
    {
        return Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray();
    }
}
