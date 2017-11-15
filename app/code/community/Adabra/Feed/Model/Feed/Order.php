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

            $singleItemDiscount = 0;

            if($orderItem->getQtyOrdered()>0) {
                $singleItemDiscount = $orderItem->getDiscountAmount() / $orderItem->getQtyOrdered();
            }


            $price = $this->_toCurrency($orderItem->getPrice() - $singleItemDiscount, true);
            $priceInclTax = $this->_toCurrency($orderItem->getPriceInclTax() - $singleItemDiscount, true);

            $cpId = 0;
            $buyRequest = $orderItem->getProductOptionByCode('info_buyRequest');
            if (isset($buyRequest['cpid'])) {
                $cpId = $buyRequest['cpid'];
            }

            if ($cpId) {
                $product = Mage::getModel('catalog/product')->load($cpId);
                $productSku = $product->getSku();
            } else {
                if ($orderItem->getProductId()) {
                    $product->setId($orderItem->getProductId());
                }

                $productSku = Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue($orderItem->getProductId(), 'sku', $storeId);
            }

            // Fake product to retrieve categories
            $categoryIds = $resourceProduct->getCategoryIds($product);

            if (!$productSku) {
                $productSku = $orderItem->getSku();
            }

            $mainCategoryId = Mage::helper('adabra_feed')->getFirstValidCategory($categoryIds, $this->getStoreId());

            $return[] = array(
                $customerId,
                $incrementId,
                $mainCategoryId,
                $productSku,
                $orderItem->getQtyOrdered(),
                $order->getOrderCurrencyCode(),
                $price,
                ($isFirstRow ? $this->_toCurrency($shippingAmount, true) : ''),
                $priceInclTax,
                ($isFirstRow ? $couponCode : ''),
                $this->_toTimestamp($createdAt),
            );
            $rowsCount++;
        }

        return $return;
    }
}
