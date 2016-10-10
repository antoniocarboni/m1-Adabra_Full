<?php
class Adabra_Tracking_Model_Queue
{
    /**
     * Add actions to queue
     * @param $key
     * @param $value
     * @return array
     */
    public function addAction($key, $value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $actions = $this->getActions();
        $actions[] = array(
            'key' => $key,
            'value' => $value,
        );

        Mage::getSingleton('core/session')->setAdabraTracking($actions);

        return $this->getActions();
    }

    /**
     * Clear actions queue
     * @return $this
     */
    public function clearQueue()
    {
        Mage::getSingleton('core/session')->setAdabraTracking(array());
        return $this;
    }

    /**
     * Get actions queue
     * @return array
     */
    public function getActions()
    {
        $res = Mage::getSingleton('core/session')->getAdabraTracking();
        if (!$res) {
            return array();
        }

        return $res;
    }
}
