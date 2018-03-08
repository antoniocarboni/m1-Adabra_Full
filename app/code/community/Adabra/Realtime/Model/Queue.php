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

    const TYPE_PRODUCT = 'product';
    const TYPE_CATEGORY = 'category';

    const STATUS_CODE_SUCCESS = 'ENJOY_THE_EXPERIENCE';

    protected $_store = null;

    protected function _construct()
    {
        $this->_init('adabra_realtime/queue');
    }

    private function _allFeedExported($resultArray) {
        if(!count($resultArray)) {
            return false;
        }

        foreach ($resultArray as $value) {
            if($value !== true) {
                return false;
            }
        }

        return true;
    }

    public function processQueue()
    {

        $queueCollection = $this->getCollection()
            ->addFieldToFilter('queue_type', array('eq' => Adabra_Realtime_Model_Queue::TYPE_PRODUCT))
            ->addFieldToFilter('updated_at', array('null' => true));

        $productsSku = $queueCollection->getColumnValues('queue_code');

        if (count($productsSku) > 0) {
            /** @var Adabra_Realtime_Model_Api_Product_Update $api */
            $api = Mage::getSingleton('adabra_realtime/api_product_update');

            /** @var Adabra_Feed_Model_Resource_Feed_Collection $feeds */
            $feeds = Mage::getModel('adabra_feed/feed')
                ->getCollection()
                ->addFieldToFilter('enabled', '1');

            $allFeedsExported = array();

            /** @var Adabra_Feed_Model_Feed $feed */
            foreach ($feeds as $feed) {
                $allFeedsExportedStatus[$feed->getStoreId()] = false;
                /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
                $productCollection = Mage::getModel('catalog/product')->getCollection();

                $productCollection
                    ->setStoreId($feed->getStoreId())
                    ->addStoreFilter()
                    ->addAttributeToSelect('*')
                    ->addWebsiteFilter($feed->getStore()->getWebsiteId())
                    ->addUrlRewrite()
                    ->addCategoryIds()
                    ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                    ->addFieldToFilter('sku', array('in' => $productsSku))
                    ->load();

                try {
                    $jsonData = $api->send($productCollection, $feed);
                    $data = json_decode($jsonData);
                    if ($data->returnStatusCode === self::STATUS_CODE_SUCCESS) {
                        $allFeedsExportedStatus[$feed->getStoreId()] = true;
                    }
                } catch (\Exception $e) {
                    Mage::log("Adabra_Realtime: Error product api call - " . $e->getMessage());
                }
            }

            if($this->_allFeedExported($allFeedsExportedStatus)) {
                foreach ($queueCollection as $item) {
                    $item->delete();
                }
            }


        }
    }
}
