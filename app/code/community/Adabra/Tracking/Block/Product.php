<?php
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
