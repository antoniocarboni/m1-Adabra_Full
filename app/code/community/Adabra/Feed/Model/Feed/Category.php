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

class Adabra_Feed_Model_Feed_Category extends Adabra_Feed_Model_Feed_Abstract
{
    protected $_type = 'category';
    protected $_exportName = 'category';

    const FAKE_CATEGORY_NAME = 'NONE';
    const FAKE_CATEGORY_ID = 'none';

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
