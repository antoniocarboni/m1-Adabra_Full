<?php
class Adabra_Feed_Model_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Get feeds list
     * @return array
     */
    public function getList()
    {
        $return = array();

        $feeds = Mage::getModel('adabra_feed/feed')->getCollection();
        foreach ($feeds as $feed) {
            $feedObj = new stdClass();
            $feedObj->code = $feed->getCode('store');
            $feedObj->status_product = $feed->getStatusProduct();
            $feedObj->status_category = $feed->getStatusCategory();
            $feedObj->status_order = $feed->getStatusOrder();
            $feedObj->status_customer = $feed->getStatusCustomer();
            $feedObj->update_time = $feed->getUpdatedAt();
            $feedObj->url_product = $feed->getFeedTypeInstance(Adabra_Feed_Model_Source_Type::TYPE_PRODUCT)->getUrl();
            $feedObj->url_category = $feed->getFeedTypeInstance(Adabra_Feed_Model_Source_Type::TYPE_CATEGORY)->getUrl();
            $feedObj->url_order = $feed->getFeedTypeInstance(Adabra_Feed_Model_Source_Type::TYPE_ORDER)->getUrl();
            $feedObj->url_customer = $feed->getFeedTypeInstance(Adabra_Feed_Model_Source_Type::TYPE_CUSTOMER)->getUrl();
            $feedObj->active = (bool) $feed->getEnabled();

            $return[] = $feedObj;
        }

        return $return;
    }

    /**
     * Rebuild feeds
     * @return array
     */
    public function rebuild()
    {
        Mage::getSingleton('adabra_feed/feed')->rebuildAll();
        return $this->getList();
    }
}
