<?php
class Adabra_Feed_Model_Source_Vfield extends Adabra_Feed_Model_Source_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'brand', 'label' => 'Brand'),
            array('value' => 'modello', 'label' => 'Model'),
            array('value' => 'prezzo_spedizione', 'label' => 'Shipping Price'),
            array('value' => 'info_pagamento', 'label' => 'Payment Information'),
            array('value' => 'tempo_spedizione', 'label' => 'Shipping Time'),
            array('value' => 'info_spedizione', 'label' => 'Shipping Information'),
            array('value' => 'fine_validita', 'label' => 'Valid To'),
            array('value' => 'disponibile_dal', 'label' => 'Valid From'),
            array('value' => 'priorita', 'label' => 'Priority'),
            array('value' => 'condizione', 'label' => 'Condition'),
            array('value' => 'f_peradulti', 'label' => 'Adults Only'),
            array('value' => 'GTIN', 'label' => 'GTIN'),
            array('value' => 'UPC', 'label' => 'UPC'),
            array('value' => 'EAN', 'label' => 'EAN\''),
            array('value' => 'ISBN', 'label' => 'ISBN'),
            array('value' => 'ASIN', 'label' => 'ASIN'),
            array('value' => 'PZN', 'label' => 'PZN'),
            array('value' => 'CNET', 'label' => 'CNET'),
            array('value' => 'MUZEID', 'label' => 'MUZEID'),
            array('value' => 'MPN', 'label' => 'MPN'),
        );
    }
}
