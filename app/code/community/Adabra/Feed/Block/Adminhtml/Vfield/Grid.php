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

        $this->addColumn('vfield_type', array(
            'header' => Mage::helper('adabra_feed')->__('Type'),
            'index' => 'vfield_type',
            'type' => 'options',
            'options' => Mage::getSingleton('adabra_feed/source_vfield_type')->toHashArray(),
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
