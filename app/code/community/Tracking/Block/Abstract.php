<?php
abstract class Adabra_Tracking_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Return true if module can be shown
     * @return bool
     */
    public function canShow()
    {
        return Mage::helper('adabra_tracking')->getEnabled();
    }

    /**
     * Get payload
     * @param $key
     * @param $values
     * @return array
     */
    public function getPayload($key, $values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        $res = array($key);
        foreach ($values as $value) {
            $res[] = $value;
        }

        return $res;
    }
}
