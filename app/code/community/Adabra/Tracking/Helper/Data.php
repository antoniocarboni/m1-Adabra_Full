<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   Adabra
 * @package    Adabra_Tracking
 * @copyright  Copyright (c) 2017 Skeeller srl / MageSpecialist (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Adabra_Tracking_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERAL_ENABLED = 'adabra_tracking/general/enabled';
    const XML_PATH_GENERAL_TEST_MODE = 'adabra_tracking/general/test_mode';
    const XML_PATH_GENERAL_SITE_ID = 'adabra_tracking/general/site_id';
    const XML_PATH_GENERAL_CATALOG_ID = 'adabra_tracking/general/catalog_id';
    const XML_PATH_GENERAL_SKU_BLACKLIST = 'adabra_tracking/general/sku_blacklist';

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
     * Get a list of blacklisted SKUs
     * @return array
     */
    public function getSkuBlackList()
    {
        return preg_split('/[\r\n]+/', trim(Mage::getStoreConfig(self::XML_PATH_GENERAL_SKU_BLACKLIST)));
    }

    /**
     * Return true if SKU is blacklisted
     * @param string $sku
     * @return bool
     */
    public function isBlacklistedSku($sku)
    {
        return in_array($sku, $this->getSkuBlackList());
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
