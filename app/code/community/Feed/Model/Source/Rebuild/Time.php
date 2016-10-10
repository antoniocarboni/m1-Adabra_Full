<?php
class Adabra_Feed_Model_Source_Rebuild_Time extends Adabra_Feed_Model_Source_Abstract
{
    public function toOptionArray()
    {
        $return = array();

        for ($i=0; $i<24; $i++) {
            $hr = $i;
            if ($hr > 12) {
                $hr %= 12;
            }

            $label = ($i < 13) ? Mage::helper('adabra_feed')->__('%s:00 am', $hr) :
                Mage::helper('adabra_feed')->__('%s:00 pm', $hr);

            $return[] = array(
                'value' => $i,
                'label' => $label,
            );
        }

        return $return;
    }
}
