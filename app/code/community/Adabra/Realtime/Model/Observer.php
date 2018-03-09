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

class Adabra_Realtime_Model_Observer
{
    public function catalogProductSaveAfter($observer)
    {
        if (Mage::helper('adabra_realtime')->getEnabled()) {
            $product = $observer->getEvent()->getProduct();

            $adabraQueue = Mage::getSingleton('adabra_realtime/queue');
            $adabraQueue->setQueueCode($product->getSku());
            $adabraQueue->setQueueType(Adabra_Realtime_Model_Queue::TYPE_PRODUCT);
            $adabraQueue->save();
        }
    }

    public function catalogCategorySaveAfter($observer)
    {
        if (Mage::helper('adabra_realtime')->getEnabled()) {
            $category = $observer->getEvent()->getCategory();

            $adabraQueue = Mage::getSingleton('adabra_realtime/queue');
            $adabraQueue->setQueueCode($category->getId());
            $adabraQueue->setQueueType(Adabra_Realtime_Model_Queue::TYPE_CATEGORY);
            $adabraQueue->save();
        }
    }
}
