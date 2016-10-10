<?php
class Adabra_Feed_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_BATCH_SIZE = 'adabra_feed/batch_size';
    const XML_PATH_ORDER_STATES = 'adabra_feed/order/states';

    const XML_PATH_GENERAL_CRON = 'adabra_feed/general/use_cron';
    const XML_PATH_GENERAL_REBUILD_TIME = 'adabra_feed/general/rebuild_time';

    const XML_PATH_HTTP_ENABLED = 'adabra_feed/http/enabled';
    const XML_PATH_HTTP_USER = 'adabra_feed/http/user';
    const XML_PATH_HTTP_PASS = 'adabra_feed/http/pass';

    const XML_PATH_FTP_ENABLED = 'adabra_feed/ftp/enabled';
    const XML_PATH_FTP_USER = 'adabra_feed/ftp/user';
    const XML_PATH_FTP_PASS = 'adabra_feed/ftp/pass';
    const XML_PATH_FTP_HOST = 'adabra_feed/ftp/host';
    const XML_PATH_FTP_PATH = 'adabra_feed/ftp/path';
    const XML_PATH_FTP_PORT = 'adabra_feed/ftp/port';
    const XML_PATH_FTP_SSL = 'adabra_feed/ftp/ssl';
    const XML_PATH_FTP_PASSIVE = 'adabra_feed/ftp/passive';

    /**
     * Return true when products batch mode is enabled
     * @param string $type
     * @return bool
     */
    public function isBatchEnabled($type)
    {
        return ($this->getBatchSize($type) > 0);
    }

    /**
     * Get products batch size
     * @param string $type
     * @return int
     */
    public function getBatchSize($type)
    {
        $xmlPath = static::XML_PATH_BATCH_SIZE.'/'.$type;
        return intval(Mage::getStoreConfig($xmlPath));
    }

    /**
     * Get order states for export
     * @return array
     */
    public function getOrderStates()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_ORDER_STATES));
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
     * Get rebuild time
     * @return array
     */
    public function getRebuildTime()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_GENERAL_REBUILD_TIME));
    }

    /**
     * Is http download enabled
     * @return bool
     */
    public function isHttpEnabled()
    {
        if (!Mage::getStoreConfig(self::XML_PATH_HTTP_ENABLED)) {
            return false;
        }

        return ($this->getHttpAuthUser() && $this->getHttpAuthPassword());
    }

    /**
     * Get auth username
     * @return string
     */
    public function getHttpAuthUser()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_HTTP_USER));
    }

    /**
     * Get auth password
     * @return string
     */
    public function getHttpAuthPassword()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_HTTP_PASS));
    }

    /**
     * Get FTP user
     * @return string
     */
    public function getFtpUser()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_FTP_USER));
    }

    /**
     * Get FTP pass
     * @return string
     */
    public function getFtpPass()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_FTP_PASS));
    }

    /**
     * Get FTP path
     * @return string
     */
    public function getFtpPath()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_FTP_PATH));
    }

    /**
     * Get FTP host
     * @return string
     */
    public function getFtpHost()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_FTP_HOST));
    }

    /**
     * Get FTP port
     * @return int
     */
    public function getFtpPort()
    {
        return intval(trim(Mage::getStoreConfig(self::XML_PATH_FTP_PORT))) ?: 21;
    }

    /**
     * Get SSL mode
     * @return bool
     */
    public function getFtpSsl()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_FTP_SSL);
    }

    /**
     * Get passive mode
     * @return bool
     */
    public function getFtpPassive()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_FTP_PASSIVE);
    }

    /**
     * Is ftp enabled
     * @return bool
     */
    public function isFtpEnabled()
    {
        if (!Mage::helper(self::XML_PATH_FTP_ENABLED)) {
            return false;
        }

        return ($this->getFtpUser() && $this->getFtpPass() && $this->getFtpHost());
    }
}
