<?php
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
