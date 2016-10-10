<?php
class Adabra_Feed_Block_Adminhtml_Feed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'adabra_feed';
        $this->_controller = 'adminhtml_feed';
        $this->_mode = 'edit';

        $this->_headerText = $this->getRequest()->getParam('id')
            ? $this->__('Edit feed')
            : $this->__('New feed');

        parent::_construct();

        $this->removeButton('delete');
    }
}
