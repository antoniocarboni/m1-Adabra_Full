<?php
class Adabra_Feed_Model_Vfield extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('adabra_feed/vfield');
    }

    /**
     * Get computed value
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getComputedValue(Mage_Catalog_Model_Product $product)
    {
        switch ($this->getMode()) {
            case Adabra_Feed_Model_Source_Vfield_Mode::MODE_STATIC:
                return $this->getValue();

            case Adabra_Feed_Model_Source_Vfield_Mode::MODE_EMPTY:
                return '';

            case Adabra_Feed_Model_Source_Vfield_Mode::MODE_MAP:
                $attributeCode = $this->getValue();

                if (!$product->getResource()->getAttribute($attributeCode)) {
                    return '';
                }

                return $product->getAttributeText($this->getValue()) ?: $product->getData($this->getValue());

            case Adabra_Feed_Model_Source_Vfield_Mode::MODE_MODEL:
                $hash = Mage::getSingleton($this->getValue())->toHashArray();

                if (isset($hash[$this->getValue()])) {
                    return $hash[$this->getValue()];
                }

                return '';
        }

        return '';
    }
}
