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
