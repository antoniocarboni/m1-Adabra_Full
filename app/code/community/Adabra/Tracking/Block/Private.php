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
 * @package    Adabra_Tracking
 * @copyright  Copyright (c) 2017 Skeeller srl / MageSpecialist (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Adabra_Tracking_Block_Private extends Adabra_Tracking_Block_Abstract
{
    /**
     * Return cart products information
     * @return array
     */
    public function getCartProductInfo()
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        $quoteItems = $quote->getAllVisibleItems();

        $out = array(
            'ids' => array(),
            'qty' => array(),
        );

        foreach ($quoteItems as $quoteItem) {
          $out['ids'][] = $quoteItem->getProduct()->getData('sku');
            $out['qty'][] = $quoteItem->getQty();
        }

        return $out;
    }

    /**
     * Get tracking properties
     * @return array
     */
    public function getTrackingProperties()
    {
        $cartProductsInformation = $this->getCartProductInfo();

        return array(
            array('key' => 'setCtxParamProductIds', 'value' => implode(',', $cartProductsInformation['ids'])),
            array('key' => 'setCtxParamProductQuantities', 'value' => implode(',', $cartProductsInformation['qty'])),
        );
    }
}
