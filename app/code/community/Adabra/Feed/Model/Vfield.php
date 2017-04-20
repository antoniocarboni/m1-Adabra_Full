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
