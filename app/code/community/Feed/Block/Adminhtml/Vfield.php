<?php
class Adabra_Feed_Block_Adminhtml_Vfield extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'adabra_feed';
        $this->_controller = 'adminhtml_vfield';

        $this->_headerText = Mage::helper('adabra_feed')->__('Virtual Fields List');

        parent::__construct();

        $this->removeButton('new');
        $this->removeButton('add');
    }
}
