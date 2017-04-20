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

class Adabra_Feed_Model_Source_Order_States
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Sales_Model_Order::STATE_COMPLETE,
                'label'=>Mage::helper('adabra_feed')->__('Complete')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_CLOSED,
                'label'=>Mage::helper('adabra_feed')->__('Closed')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_HOLDED,
                'label'=>Mage::helper('adabra_feed')->__('Holded')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_CANCELED,
                'label'=>Mage::helper('adabra_feed')->__('Canceled')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_NEW,
                'label'=>Mage::helper('adabra_feed')->__('New')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW,
                'label'=>Mage::helper('adabra_feed')->__('Payment Review')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                'label'=>Mage::helper('adabra_feed')->__('Pending Payment')
            ), array(
                'value' => Mage_Sales_Model_Order::STATE_PROCESSING,
                'label'=>Mage::helper('adabra_feed')->__('Processing')
            ),
        );
    }
}
