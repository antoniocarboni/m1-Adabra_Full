<?php
class Adabra_Tracking_Block_Category extends Adabra_Tracking_Block_Abstract
{
    /**
     * Get current category
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    public function getTrackingProperties()
    {
        return array(
            array('key' => 'trkCategoryView', 'value' => $this->getCategory()->getId()),
        );
    }
}
