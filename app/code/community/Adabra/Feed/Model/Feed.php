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

class Adabra_Feed_Model_Feed extends Mage_Core_Model_Abstract
{
    protected $_store = null;

    protected function _construct()
    {
        $this->_init('adabra_feed/feed');
    }

    /**
     * Get feed store
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore($this->getStoreId());
        }

        return $this->_store;
    }

    /**
     * Get feed code
     * @param string $scope
     * @return string
     */
    public function getCode($scope = 'store')
    {
        if ($scope == 'website') {
            return strtolower($this->getStore()->getWebsite()->getCode());
        }

        return strtolower(implode('_', array(
            $this->getStore()->getWebsite()->getCode(),
            $this->getStore()->getCode(),
            $this->getCurrency()
        )));
    }

    /**
     * Get feed instance
     * @param $type
     * @return Adabra_Feed_Model_Feed_Abstract
     * @throws Mage_Exception
     */
    public function getFeedTypeInstance($type)
    {
        if (!$this->_isValidType($type)) {
            Mage::throwException('Unknown feed type '.$type);
            return;
        }

        $feed = Mage::getModel('adabra_feed/feed_'.$type);
        $feed->setFeed($this);

        return $feed;
    }

    /**
     * Get feed type instance by code
     * @param $code
     * @return Adabra_Feed_Model_Feed_Abstract|null
     */
    public function getFeedTypeInstanceByCode($code)
    {
        $feeds = Mage::getModel('adabra_feed/feed')->getCollection();
        foreach ($feeds as $feed) {
            $types = Mage::getSingleton('adabra_feed/source_type')->toArray();
            foreach ($types as $type) {
                $feedInstance = $feed->getFeedTypeInstance($type);

                if ($feedInstance->getCode() == $code) {
                    return $feedInstance;
                }
            }
        }

        return null;
    }

    /**
     * Export feed
     * @param string $type
     * @return $this
     */
    public function export($type)
    {
        $feed = $this->getFeedTypeInstance($type);
        $feed->export();
        return $this;
    }

    /**
     * Return true if type is valid
     * @param $type
     * @return bool
     */
    protected function _isValidType($type)
    {
        $types = Mage::getSingleton('adabra_feed/source_type')->toArray();
        return in_array($type, $types);
    }

    /**
     * Change feed status at website level
     * @param $type
     * @param $newStatus
     * @return $this
     */
    public function changeStatusForWebsite($type, $newStatus)
    {
        $store = $this->getStore();
        $website = $store->getWebsite();

        $websiteStores = array();
        foreach ($website->getStores() as $store) {
            $websiteStores[] = $store->getId();
        }

        $websiteFeeds = Mage::getModel('adabra_feed/feed')->getCollection();
        $websiteFeeds
            ->addFieldToFilter('store_id', array('in' => $websiteStores))
            ->addFieldToFilter('enabled', 1);

        foreach ($websiteFeeds as $websiteFeed) {
            $websiteFeed->changeStatus($type, $newStatus);
        }

        return $this;
    }

    /**
     * Change feed status
     * @param $type
     * @param $newStatus
     * @return $this
     */
    public function changeStatus($type, $newStatus)
    {
        if ($this->_isValidType($type)) {
            $this
                ->setData('status_'.$type, $newStatus)
                ->setUpdatedAt(Mage::getSingleton('core/date')->date('Y-m-d H:i:s'))
                ->save();
        }

        return $this;
    }

    /**
     * Mark feed for rebuild
     * @return $this
     */
    public function rebuild()
    {
        $types = Mage::getSingleton('adabra_feed/source_type')->toArray();
        foreach ($types as $type) {
            $this->setData('status_'.$type, Adabra_Feed_Model_Source_Status::MARKED_REBUILD);
        }

        $this
            ->setUpdatedAt(Mage::getSingleton('core/date')->date('Y-m-d H:i:s'))
            ->save();

        return $this;
    }

    /**
     * Mark all feeds for rebuild
     * @return $this
     */
    public function rebuildAll()
    {
        $collection = Mage::getModel('adabra_feed/feed')->getCollection();
        foreach ($collection as $feed) {
            $feed->rebuild();
        }

        return $this;
    }

    /**
     * Export next feed
     * @return $this
     */
    public function exportNext()
    {
        $fsHelper = Mage::helper('adabra_feed/filesystem');

        if (!$fsHelper->acquireLock('feed')) {
            return $this;
        }

        $types = Mage::getSingleton('adabra_feed/source_type')->toArray();
        $collection = Mage::getModel('adabra_feed/feed')->getCollection();
        $collection
            ->addFieldToFilter('enabled', 1);

        $coreRead = Mage::getSingleton('core/resource')->getConnection('default_read');

        $conditions = array();
        foreach ($types as $type) {
            $conditions[] =
                '(status_'.$type.' != '.$coreRead->quote(Adabra_Feed_Model_Source_Status::READY).')';
        }

        $collection->getSelect()->where(implode(' OR ', $conditions));

        if ($collection->getSize()) {
            /** @var $feed Adabra_Feed_Model_Feed */

            // @codingStandardsIgnoreStart
            $feed = $collection->getFirstItem();
            // @codingStandardsIgnoreEnd

            foreach ($types as $type) {
                if ($feed->getData('status_'.$type) == Adabra_Feed_Model_Source_Status::READY) {
                    continue;
                }

                $feed->export($type);
                break;
            }
        }

        $fsHelper->releaseLock($this->_lockName);

        return $this;
    }
}
