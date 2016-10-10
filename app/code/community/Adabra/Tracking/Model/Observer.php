<?php
class Adabra_Tracking_Model_Observer
{
    public function checkoutCartProductAddAfter($event)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $event->getEvent()->getProduct();

        $productSku = $product->getSku();

        Mage::getSingleton('adabra_tracking/queue')->addAction('trkProductBasketAdd', $productSku);
    }

    protected function _quoteRemoveItem(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        $productSku = $quoteItem->getSku();
        Mage::getSingleton('adabra_tracking/queue')->addAction('trkProductBasketRemove', $productSku);
    }

    public function salesQuoteRemoveItem($event)
    {
        /** @var $quoteItem Mage_Sales_Model_Quote_Item */
        $quoteItem = $event->getEvent()->getQuoteItem();
        $this->_quoteRemoveItem($quoteItem);
    }

    public function customerRegisterSuccess($event)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $event->getEvent()->getCustomer();

        Mage::getSingleton('adabra_tracking/queue')->addAction('trkUserRegistration', $customer->getId());
    }

    /**
     * Convert value to currency
     * @param $val
     * @return string
     */
    protected function _toCurrency($val)
    {
        return number_format($val, 4, '.', '');
    }

    /**
     * Convert value to currency
     * @param $ts
     * @return string
     */
    protected function _toTimestamp($ts)
    {
        return
            Mage::getSingleton('core/date')->date('Y-m-d', $ts).'T'
            .Mage::getSingleton('core/date')->date('H:i:s', $ts);
    }

    public function salesOrderPlaceAfter($event)
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $event->getOrder();

        $queue = Mage::getSingleton('adabra_tracking/queue');

        $rowsCount = 0;
        $createdAt = Varien_Date::toTimestamp($order->getCreatedAt());

        $orderItems = $order->getAllVisibleItems();

        foreach ($orderItems as $orderItem) {
            $isFirstRow = ($rowsCount == 0);

            $productSku = $orderItem->getSku();

            $couponCode = $order->getCouponCode();
            if (is_null($couponCode)) {
                $couponCode = '';
            }

            /** @var $orderItem Mage_Sales_Model_Order_Item */
            $queue->addAction('trkProductSale', array(
                $order->getIncrementId(),
                $productSku,
                $orderItem->getQtyOrdered(),
                $isFirstRow ? $couponCode : '',
                $orderItem->getPrice(),
                $orderItem->getPriceInclTax(),
                $isFirstRow ? $this->_toCurrency($order->getShippingAmount()) : '',
                $order->getOrderCurrencyCode(),
                $this->_toTimestamp($createdAt),
            ));

            $rowsCount++;
        }
    }

    public function controllerActionPredispatchCheckoutCartUpdatePost($event)
    {
        $post = Mage::app()->getRequest()->getPost('update_cart_action');
        if ($post == 'empty_cart') {
            $quote = Mage::helper('checkout/cart')->getQuote();
            $quoteItems = $quote->getAllItems();

            foreach ($quoteItems as $quoteItem) {
                $this->_quoteRemoveItem($quoteItem);
            }
        }
    }
}
