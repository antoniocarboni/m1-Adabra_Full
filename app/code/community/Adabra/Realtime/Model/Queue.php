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
 * @package    Adabra_Feed
 * @copyright  Copyright (c) 2017 Skeeller srl / MageSpecialist (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Adabra_Realtime_Model_Queue extends Mage_Core_Model_Abstract
{
    protected $_store = null;

    protected function _construct()
    {
        $this->_init('adabra_realtime/queue');
    }

    public function processQueue()
    {

        $model = $this->getCollection();

        $productsSku = $model->getColumnValues('product_sku');

        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addFieldToFilter('sku', array('in' => $productsSku))
            ->load();

        /** @var Adabra_Realtime_Model_Api_Product_All $api */
        $api = Mage::getModel('adabra_realtime/api_product_all');

        try {
            $jsonData = $api->send($productCollection);
            $data = json_decode($jsonData);
        }
        catch (\Exception $e) {
            $data = '';
        }

//        $row->delete();
    }
}
