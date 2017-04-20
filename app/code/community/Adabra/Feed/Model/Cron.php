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

class Adabra_Feed_Model_Cron
{
    public function buildNext(Varien_Event_Observer $event)
    {
        if (Mage::helper('adabra_feed')->isCronEnabled()) {
            Mage::getSingleton('adabra_feed/feed')->exportNext();
        }
    }

    public function rebuildFeeds(Varien_Event_Observer $event)
    {
        if (Mage::helper('adabra_feed')->isCronEnabled()) {
            $hr = intval(Mage::getSingleton('core/date')->date('H'));

            $rebuildHours = Mage::helper('adabra_feed')->getRebuildTime();
            if (in_array($hr, $rebuildHours)) {
                Mage::getSingleton('adabra_feed/feed')->rebuildAll();
            }
        }
    }
}
