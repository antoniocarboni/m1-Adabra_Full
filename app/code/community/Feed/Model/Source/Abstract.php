<?php
abstract class Adabra_Feed_Model_Source_Abstract
{
    public function toHashArray()
    {
        $return = array();

        $options = $this->toOptionArray();
        foreach ($options as $option) {
            $return[$option['value']] = $option['label'];
        }

        return $return;
    }

    public function toArray()
    {
        return array_keys($this->toHashArray());
    }
}
