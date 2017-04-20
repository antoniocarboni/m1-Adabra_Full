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

class Adabra_Tracking_Model_Container_Nocache extends Enterprise_PageCache_Model_Container_Abstract
{
    protected function _getIdentifier()
    {
        return microtime();
    }

    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        return false;
    }

    protected function _renderBlock()
    {
        /** @var $block Mage_Reports_Block_Product_Abstract */
        $block = $this->_getPlaceHolderBlock();
        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->toHtml();
    }

    protected function _getCacheId()
    {
        return 'ADABRA_TRACKING' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }
}
