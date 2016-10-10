<?php
class Adabra_Tracking_Model_Pagetype
{
    const TYPE_HOMEPAGE = 101;
    const TYPE_CATEGORY = 102;
    const TYPE_PRODUCT = 103;
    const TYPE_CART = 104;
    const TYPE_SEARCH = 105;
    const TYPE_LANDING = 106;
    const TYPE_404 = 107;
    const TYPE_OTHER = 108;
    const TYPE_REGISTRATION = 109;
    const TYPE_CHECKOUT = 110;

    /**
     * Return true if there is match in current page
     * @param array $pageType
     * @return bool
     */
    protected function _hasMatch($pageType)
    {
        $currentHandles = Mage::app()->getLayout()->getUpdate()->getHandles();
        $handles = Mage::helper('adabra_tracking')->getPageTypeHandles($pageType);

        return (bool) (count(array_intersect($handles, $currentHandles)));
    }

    /**
     * Get page type from current controller
     * @return string
     */
    public function getCurrentPageType()
    {
        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_HOMEPAGE)) {
            return self::TYPE_HOMEPAGE;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_CATEGORY)) {
            return self::TYPE_CATEGORY;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_PRODUCT)) {
            return self::TYPE_PRODUCT;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_CART)) {
            return self::TYPE_CART;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_SEARCH)) {
            return self::TYPE_SEARCH;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_LANDING)) {
            return self::TYPE_LANDING;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_404)) {
            return self::TYPE_404;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_REGISTRATION)) {
            return self::TYPE_REGISTRATION;
        }

        if ($this->_hasMatch(Adabra_Tracking_Helper_Data::TYPE_CHECKOUT)) {
            return self::TYPE_CHECKOUT;
        }

        return self::TYPE_OTHER;
    }
}
