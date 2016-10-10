<?php
class Adabra_Feed_Block_Adminhtml_Vfield_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'adabra_feed';
        $this->_controller = 'adminhtml_vfield';
        $this->_mode = 'edit';

        $this->_headerText = $this->__('Edit Virtual Field');
    }
}
