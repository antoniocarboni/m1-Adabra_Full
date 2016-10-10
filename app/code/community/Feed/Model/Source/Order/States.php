<?php
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
