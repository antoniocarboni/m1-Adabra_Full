<?php
class Adabra_Tracking_Block_Queue extends Adabra_Tracking_Block_Abstract
{
    public function getTrackingProperties()
    {
        $queue = Mage::getSingleton('adabra_tracking/queue');
        $queuedActions = $queue->getActions();
        $queue->clearQueue();

        return $queuedActions;
    }
}
