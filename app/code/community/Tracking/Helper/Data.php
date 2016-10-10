<?php
class Adabra_Tracking_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERAL_ENABLED = 'adabra_tracking/general/enabled';
    const XML_PATH_GENERAL_TEST_MODE = 'adabra_tracking/general/test_mode';
    const XML_PATH_GENERAL_SITE_ID = 'adabra_tracking/general/site_id';
    const XML_PATH_GENERAL_CATALOG_ID = 'adabra_tracking/general/catalog_id';

    const XML_PATH_PAGE_TYPE = 'adabra_tracking/page_types/';
    const TYPE_HOMEPAGE = 'homepage';
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_SEARCH = 'search';
    const TYPE_LANDING = 'landing';
    const TYPE_404 = 'not_found';
    const TYPE_REGISTRATION = 'registration';
    const TYPE_CHECKOUT = 'checkout';

    const TRACKING_HOST_PROD = 'track.adabra.com';
    const TRACKING_HOST_TEST = 'staging.marketingspray.com/tracking';

    /**
     * Return true if this plugin is enabled
     * @return bool
     */
    public function getEnabled()
    {
        if (!$this->getSiteId() || !$this->getCatalogId()) {
            return false;
        }

        return (bool) Mage::getStoreConfig(self::XML_PATH_GENERAL_ENABLED);
    }

    /**
     * Return true if this plugin is in test mode
     * @return bool
     */
    public function getTestMode()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_GENERAL_TEST_MODE);
    }

    /**
     * Get site ID
     * @return string
     */
    public function getSiteId()
    {
        return trim(strtolower(Mage::getStoreConfig(self::XML_PATH_GENERAL_SITE_ID)));
    }

    /**
     * Get catalog ID
     * @return string
     */
    public function getCatalogId()
    {
        return trim(strtolower(Mage::getStoreConfig(self::XML_PATH_GENERAL_CATALOG_ID)));
    }

    /**
     * Get Adabra tracking host
     * @return string
     */
    public function getAdabraTrackingHost()
    {
        return $this->getTestMode() ? self::TRACKING_HOST_TEST : self::TRACKING_HOST_PROD;
    }

    /**
     * Get page type handles
     * @param $pageType
     * @return array
     */
    public function getPageTypeHandles($pageType)
    {
        return preg_split('/[^\w\_\-]+/', Mage::getStoreConfig(self::XML_PATH_PAGE_TYPE.$pageType));
    }
}
