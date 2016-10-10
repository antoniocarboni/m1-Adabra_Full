<?php
class Adabra_Feed_Model_Source_Vfield_Mode extends Adabra_Feed_Model_Source_Abstract
{
    const MODE_MAP = 'map';
    const MODE_STATIC = 'static';
    const MODE_EMPTY = 'empty';
    const MODE_MODEL = 'model';

    public function toOptionArray()
    {
        return array(
            array('value' => self::MODE_MAP, 'label' => 'Map to Attribute'),
            array('value' => self::MODE_STATIC, 'label' => 'Static Value'),
            array('value' => self::MODE_EMPTY, 'label' => 'Empty'),
            array('value' => self::MODE_MODEL, 'label' => 'Source Model'),
        );
    }
}
