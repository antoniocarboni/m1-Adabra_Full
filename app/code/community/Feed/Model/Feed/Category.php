<?php
class Adabra_Feed_Model_Feed_Category extends Adabra_Feed_Model_Feed_Abstract
{
    protected $_type = 'category';
    protected $_exportName = 'category';
    protected $_rootPathIds = null;

    const FAKE_CATEGORY_NAME = 'NONE';
    const FAKE_CATEGORY_ID = 'none';

    /**
     * Get root path ids
     * @return array|null
     */
    protected function _getRootPathIds()
    {
        if (is_null($this->_rootPathIds)) {
            $rootCategoryId = $this->getStore()->getRootCategoryId();
            $rootCategory = Mage::getModel('catalog/category')->load($rootCategoryId);

            $this->_rootPathIds = $rootCategory->getPathIds();
        }

        return $this->_rootPathIds;
    }

    /**
     * Prepare collection
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $this->_collection = Mage::getModel('catalog/category')->getCollection();
        $this->_collection
            ->addAttributeToSelect('*')
            ->addUrlRewriteToResult()
            ->setStoreId($this->getStoreId());
    }

    /**
     * Get virtual rows
     * @return array
     */
    protected function _getVirtualRows()
    {
        return array(array(
            self::FAKE_CATEGORY_ID,
            0,
            self::FAKE_CATEGORY_NAME,
            '',
            $this->_toBoolean(false),
        ));
    }

    /**
     * Get feed row
     * @param Varien_Object $entity
     * @return array
     */
    protected function _getFeedRow(Varien_Object $entity)
    {
//        $rootPaths = $this->_getRootPathIds();
//        if (in_array($entity->getId(), $rootPaths)) {
//            return array();
//        }

        /** @var Mage_Catalog_Model_Category $category */
        $category = $entity;

        return array(array(
            $category->getId(),
            $category->getParentId(),
            $category->getName(),
            $category->getDescription(),
            $this->_toBoolean($category->getIsActive()),
        ));
    }

    /**
     * Get headers
     * @return array
     */
    protected function _getHeaders()
    {
        return array(
            'category_id',
            'parent_category_id',
            'category_name',
            'category_description',
            'active',
        );
    }
}
