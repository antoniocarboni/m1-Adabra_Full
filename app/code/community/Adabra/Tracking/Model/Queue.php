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
