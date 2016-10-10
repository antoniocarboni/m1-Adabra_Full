<?php
class Adabra_Feed_Model_Source_Status extends Adabra_Feed_Model_Source_Abstract
{
    const BUILDING = 'building';
    const MARKED_REBUILD = 'marked-rebuild';
    const READY = 'ready';

    public function toOptionArray()
    {
        return array(
            array('value' => self::READY, 'label' => Mage::helper('adabra_feed')->__('Ready')),
            array('value' => self::BUILDING, 'label' => Mage::helper('adabra_feed')->__('Building')),
            array('value' => self::MARKED_REBUILD, 'label' => Mage::helper('adabra_feed')->__('Queue')),
        );
    }
}
