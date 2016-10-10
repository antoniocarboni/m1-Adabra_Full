<?php
class Adabra_Feed_Model_Feed_Order extends Adabra_Feed_Model_Feed_Abstract
{
    const INTERVAL_DAYS = 365;
    const ONE_DAY = 86400;

    protected $_type = 'order';
    protected $_exportName = 'orders';

    /**
     * Prepare collection
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $interval = self::INTERVAL_DAYS * self::ONE_DAY;

        $orderStates = Mage::helper('adabra_feed')->getOrderStates();

        $currentTimestamp = Mage::getSingleton('core/date')->timestamp(time());
        $dateStart = Mage::getSingleton('core/date')->date('Y-m-d', $currentTimestamp - $interval);

        $this->_collection = Mage::getModel('sales/order')->getCollection();
        $this->_collection
            ->addAttributeToFilter('store_id', array('eq' => $this->getStoreId()))
            ->addFieldToFilter('created_at', array('gteq' => $dateStart))
            ->addFieldToFilter('state', array('in' => $orderStates));
    }

    /**
     * Get headers
     * @return array
     */
    protected function _getHeaders()
    {
        return array(
            'id_utente',
            'id_raggrprod',
            'id_cli_categoria',
            'id_cli_prodotto',
            'quantita',
            'valuta',
            'prezzo_notax',
            'prezzo_spedizione',
            'prezzo',
            'coupon',
            'ts',
        );
    }

    /**
     * Get feed row
     * @param Varien_Object $entity
     * @return array
     */
    protected function _getFeedRow(Varien_Object $entity)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $entity;

        $customerId = $order->getCustomerId();
        if (!$customerId) {
            return array();
        }

        $orderItems = $order->getAllVisibleItems();

        $shippingAmount = $order->getBaseShippingAmount();
        $couponCode = $order->getCouponCode();
        $incrementId = $order->getIncrementId();
        $createdAt = Varien_Date::toTimestamp($order->getCreatedAt());

        $product = Mage::getModel('catalog/product');
        $resourceProduct = Mage::getResourceModel('catalog/product');

        $storeId = $order->getStoreId();

        $return = array();
        $rowsCount = 0;
        foreach ($orderItems as $orderItem) {
            $isFirstRow = ($rowsCount == 0);

            // Fake product to retrieve categories
            $categoryIds = array();
            if ($orderItem->getProductId()) {
                $product->setId($orderItem->getProductId());
                $categoryIds = $resourceProduct->getCategoryIds($product);
            }

            if (!count($categoryIds)) {
                $categoryIds = array(Adabra_Feed_Model_Feed_Category::FAKE_CATEGORY_ID);
            }

            $productSku = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue($orderItem->getProductId(), 'sku', $storeId);

            if (!$productSku) {
                $productSku = $orderItem->getSku();
            }

            $return[] = array(
                $customerId,
                $incrementId,
                $categoryIds[0],
                $productSku,
                $orderItem->getQtyOrdered(),
                $order->getOrderCurrencyCode(),
                $this->_toCurrency($orderItem->getPrice(), true),
                ($isFirstRow ? $this->_toCurrency($shippingAmount, true) : ''),
                $this->_toCurrency($orderItem->getPriceInclTax(), true),
                ($isFirstRow ? $couponCode : ''),
                $this->_toTimestamp($createdAt),
            );
            $rowsCount++;
        }

        return $return;
    }
}
