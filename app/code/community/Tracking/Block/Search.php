<?php
class Adabra_Tracking_Block_Search extends Adabra_Tracking_Block_Abstract
{
    /**
     * Get query string
     * @return string
     */
    public function getKw()
    {
        return Mage::helper('catalogsearch')->getQuery()->getQueryText();
    }

    public function getTrackingProperties()
    {
        return array(
            array('key' => 'trkProductLocalSearch', 'value' => $this->getKw()),
        );
    }
}
