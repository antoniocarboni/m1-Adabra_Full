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
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Adabra_Realtime_Model_Api {

    const ENDPOINT = '';
    const METHOD = 'POST';

    protected $_endpoint;

    protected function _send($payload = array())
    {
        try {
            $client = new Zend_Http_Client($this->_getEndpoint());

            $client->setHeaders('Content-type', 'application/json');

            if (!empty($payload)) {

                if(static::METHOD == 'POST'){
                    $json = Mage::helper('core')->jsonEncode($payload);
                    $client->setRawData($json, null);
                }
            }

            $response = $client->request(static::METHOD);


            if ($response->getStatus() !== 200) {
                $message = $this->_getHelper()->__("Api call failed with status: %s and error %s", $response->getStatus(), $response->getBody());
                $this->_getHelper()->log($message, Zend_Log::CRIT, true);
            }

            return $response->getBody();
        }
        catch (\Exception $e) {
            $this->_getHelper()->log("Request failed with error " . $e->getMessage(), Zend_Log::ERR, true);
            return '';
        }
    }

    protected function _getHelper()
    {
        return Mage::helper('adabra_realtime');
    }

    /**
     * @return string
     */
    protected function _getEndpoint()
    {
        if(is_null($this->_endpoint)) {
            $this->_endpoint = static::ENDPOINT;
        }

        return $this->_endpoint;
    }

    protected function _addParamsToEndpoint()
    {
        $this->_endpoint = static::ENDPOINT;

    }

    /**
     * Conver to boolean
     * @param $val
     * @return string
     */
    protected function _toBoolean($val)
    {
        return $val ? 'true' : 'false';
    }

    /**
     * Convert value to currency
     * @param $val
     * @param $currencyConvert
     * @return string
     */
    protected function _toCurrency($val, $currencyConvert = false)
    {
        if ($currencyConvert) {
            $baseCurrency = $this->getStore()->getBaseCurrencyCode();
            $val = Mage::helper('directory')->currencyConvert($val, $baseCurrency, $this->getFeed()->getCurrency());
        }

        return number_format($val, 4, '.', '');
    }
}