<?php
class Adabra_Feed_Block_Adminhtml_Feed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'adabra_feed';
        $this->_controller = 'adminhtml_feed';

        $this->_headerText = Mage::helper('adabra_feed')->__('Feeds List');
        $this->_addButtonLabel = Mage::helper('adabra_feed')->__('Add New Feed');

        $this->_addButton('rebuild', array(
            'label'     => Mage::helper('adabra_feed')->__('Rebuild Feeds'),
            'onclick'   => "self.location.href='".$this->getUrl('*/*/rebuild/')."'",
            'level'     => -1
        ));

        parent::__construct();
    }
}
