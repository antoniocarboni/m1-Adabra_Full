<?php
class Adabra_Tracking_Block_Base extends Adabra_Tracking_Block_Abstract
{
    /**
     * Get document's title
     * @return string
     */
    public function getDocumentTitle()
    {
        return $this->getLayout()->getBlock('head')->getTitle();
    }

    /**
     * Get user ID
     * @return int
     */
    public function getSiteUserId()
    {
        $helperCustomer = Mage::helper('customer');
        if ($helperCustomer->isLoggedIn()) {
            return $helperCustomer->getCustomer()->getId();
        }

        return 0;
    }

    /**
     * Get document's language
     * @return string
     */
    public function getLanguage()
    {
        return substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
    }

    /**
     * Get site ID
     * @return string
     */
    public function getSiteId()
    {
        return Mage::helper('adabra_tracking')->getSiteId();
    }

    /**
     * Get catalog ID
     * @return string
     */
    public function getCatalogId()
    {
        return Mage::helper('adabra_tracking')->getCatalogId();
    }

    /**
     * Get adabra tracking host
     * @return string
     */
    public function getAdabraTrackingHost()
    {
        return Mage::helper('adabra_tracking')->getAdabraTrackingHost();
    }

    /**
     * Return a list of cart products
     * @return array
     */
    public function getCartProductIds()
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        $quoteItems = $quote->getAllVisibleItems();

        $out = array();

        foreach ($quoteItems as $quoteItem) {
            $out[] = $quoteItem->getSku();
        }

        return $out;
    }

    /**
     * Get tracking properties
     * @return array
     */
    public function getTrackingProperties()
    {
        $pageType = Mage::getSingleton('adabra_tracking/pagetype')->getCurrentPageType();

        $res = array(
            array('key' => 'setDocumentTitle', 'value' => $this->getDocumentTitle()),
            array('key' => 'setLanguage', 'value' => $this->getLanguage()),
            array('key' => 'setSiteId', 'value' => $this->getSiteId()),
            array('key' => 'setCatalogId', 'value' => $this->getCatalogId()),
            array('key' => 'setSiteUserId', 'value' => $this->getSiteUserId()),
            array('key' => 'setCtxParamProductIds', 'value' => implode(',', $this->getCartProductIds())),
            array('key' => 'setPageType', 'value' => $pageType),
        );

        if ($category = Mage::registry('current_category')) {
            $res[] = array('key' => 'setCategoryId', 'value' => $category->getId());
        }

        if ($product = Mage::registry('current_product')) {
            $res[] = array('key' => 'setProductId', 'value' => $product->getSku());
        }

        return $res;
    }
}
