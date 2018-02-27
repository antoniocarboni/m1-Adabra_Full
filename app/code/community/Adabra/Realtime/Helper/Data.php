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

class Adabra_Realtime_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERAL_ENABLED = 'adabra_realtime/general/enabled';
    const XML_PATH_GENERAL_CRON = 'adabra_realtime/general/use_cron';
    const XML_COMPANY_ID = 'adabra_realtime/general/company_id';
    const XML_API_KEY = 'adabra_realtime/general/api_key';
    const XML_API_SECRET = 'adabra_realtime/general/api_secret';
    const XML_CATALOG_ID = 'adabra_realtime/general/catalog_id';

    /**
     * Return true if this plugin is enabled
     * @return bool
     */
    public function getEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_GENERAL_ENABLED);
    }

    /**
     * Return true if cron mode is enabled
     * @return bool
     */
    public function isCronEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_GENERAL_CRON);
    }

    /**
     * Get the company id from config
     * @return string
     */
    public function getCompanyId()
    {
        return Mage::getStoreConfig(static::XML_COMPANY_ID);
    }

    /**
     * Get the api key from config
     * @return string
     */
    public function getApiKey()
    {
        return Mage::getStoreConfig(static::XML_API_KEY);
    }

    /**
     * Get the Catalog Id from config
     * @return string
     */
    public function getCatalogId()
    {
        return Mage::getStoreConfig(static::XML_CATALOG_ID);
    }

    /**
     * Get the api secret from config
     * @return string
     */
    public function getApiSecret()
    {
        return Mage::getStoreConfig(static::XML_API_SECRET);
    }

    /**
     * @param $message
     * @param int $level
     * @param bool $force
     */
    public function log($message, $level = Zend_Log::INFO, $force = false)
    {
        Mage::log($message, $level, 'adabra_realtime.log', $force);
    }
}
