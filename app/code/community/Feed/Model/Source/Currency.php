<?php
class Adabra_Feed_Model_Source_Currency extends Adabra_Feed_Model_Source_Abstract
{
    public function toOptionArray()
    {
        $return = array();

        $currencyCodes = Mage::getSingleton('directory/currency')->getConfigAllowCurrencies();
        foreach ($currencyCodes as $currencyCode) {
            $return[] = array(
                'value' => $currencyCode,
                'label' => $currencyCode,
            );
        }

        return $return;
    }
}
