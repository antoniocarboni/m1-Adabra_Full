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
