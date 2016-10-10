<?php
class Adabra_Feed_Block_Adminhtml_Vfield_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('adabraVfieldGrid');
        $this->_controller = 'adminhtml_feed';
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('adabra_feed/vfield');
        $collection = $model->getCollection();
        $this->setCollection($collection);

        $this->setDefaultSort('code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header' => Mage::helper('adabra_feed')->__('Adabra Attribute'),
            'index' => 'code',
            'type' => 'text',
        ));

        $this->addColumn('mode', array(
            'header' => Mage::helper('adabra_feed')->__('Mode'),
            'index' => 'mode',
            'type' => 'options',
            'options' => Mage::getSingleton('adabra_feed/source_vfield_mode')->toHashArray(),
        ));

        $this->addColumn('value', array(
            'header' => Mage::helper('adabra_feed')->__('Value'),
            'index' => 'value',
            'type' => 'text',
        ));

        $this->addColumn(
            'action',
            array(
                'header'    => Mage::helper('adabra_feed')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption'   => Mage::helper('adabra_feed')->__('Edit'),
                    'url'       => array(
                        'base'=>'*/*/edit'
                    ),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'action',
            )
        );

            return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId(),
        ));
    }
}
