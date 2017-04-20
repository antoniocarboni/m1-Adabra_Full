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

class Adabra_Tracking_Block_Product extends Adabra_Tracking_Block_Abstract
{
    /**
     * Get current category
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get a list of tags for this product
     * @return array
     */
    protected function _getTagsList()
    {
        $tagModel = Mage::getModel('tag/tag');
        $tagsCollection = Mage::getModel('tag/tag')->getResourceCollection()
            ->addPopularity()
            ->addStatusFilter($tagModel->getApprovedStatus())
            ->addProductFilter($this->getProduct()->getId())
            ->setFlag('relation', true)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setActiveFilter()
            ->load();

        $tagsList = array();
        foreach ($tagsCollection as $tag) {
            $tagsList[] = $tag->getName();
        }

        return $tagsList;
    }

    public function getTrackingProperties()
    {
        $value = array($this->getProduct()->getSku());

        if (Mage::registry('current_category')) {
            $value[] = Mage::registry('current_category')->getId();
        } else {
            $value[] = 0;
        }

        $brand = Mage::getSingleton('adabra_feed/feed_product')->getVirtualField($this->getProduct(), 'brand');
        $value[] = $brand;

        $res = array(
            array(
                'key' => 'trkProductView',
                'value' => $value,
            ),
            array(
                'key' => 'setCtxParamBrands',
                'value' => $brand,
            )
        );

        $productTags = $this->_getTagsList();
        if (count($productTags)) {
            $res[] = array(
                'key' => 'setCtxParamTags',
                'value' => implode(',', $productTags),
            );
        }

        return $res;
    }
}
