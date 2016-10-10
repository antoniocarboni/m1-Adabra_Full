<?php
class Adabra_Feed_Model_Source_Type extends Adabra_Feed_Model_Source_Abstract
{
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';
    const TYPE_CUSTOMER = 'customer';
    const TYPE_ORDER = 'order';

    public function toOptionArray()
    {
        return array(
            array('value' => self::TYPE_CATEGORY, 'label' => Mage::helper('adabra_feed')->__('Category')),
            array('value' => self::TYPE_PRODUCT, 'label' => Mage::helper('adabra_feed')->__('Product')),
            array('value' => self::TYPE_CUSTOMER, 'label' => Mage::helper('adabra_feed')->__('Customer')),
            array('value' => self::TYPE_ORDER, 'label' => Mage::helper('adabra_feed')->__('Order')),
        );
    }
}
