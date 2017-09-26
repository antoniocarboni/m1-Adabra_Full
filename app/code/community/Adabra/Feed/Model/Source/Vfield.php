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
            array('value' => 'fidelity_card', 'label' => 'fidelity_card'),
        );
    }
}
