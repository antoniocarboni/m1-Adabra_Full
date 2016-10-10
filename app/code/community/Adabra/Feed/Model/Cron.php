<?php
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
