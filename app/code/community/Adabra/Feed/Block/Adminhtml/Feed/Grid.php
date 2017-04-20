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

class Adabra_Feed_Block_Adminhtml_Feed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('adabraFeedGrid');
        $this->_controller = 'adminhtml_feed';
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('adabra_feed/feed');
        $collection = $model->getCollection();
        $this->setCollection($collection);

        $this->setDefaultSort('store_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

        return parent::_prepareCollection();
    }

    public function decorateStatus($value, $row, $column, $isExport)
    {
        if (!$row->getData('enabled')) {
            return '-';
        }

        $status = $row->getData($column->getIndex());

        if ($status == Adabra_Feed_Model_Source_Status::BUILDING) {
            $class = 'minor';
            $text = $this->__('Building');
        } elseif ($status == Adabra_Feed_Model_Source_Status::READY) {
            $class = 'notice';
            $text = $this->__('Ready');
        } else {
            $class = 'major';
            $text = $this->__('Queue');
        }

        return '<span class="grid-severity-'.$class.'"><span>'.$text.'</span></span>';
    }

    public function decorateDate($value, $row, $column, $isExport)
    {
        $val = $row->getData($column->getIndex());

        if (!$val) {
            return '';
        }
        return Mage::helper('core')->formatDate($val, 'medium', true);
    }

    public function decorateStore($value, $row, $column, $isExport)
    {
        return $this->escapeHtml($value).'<br />'.$this->__('<strong>Feed code:</strong> %s', $row->getCode());
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'        => Mage::helper('adabra_feed')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'index'         => 'adabra_feed_id',
        ));

        $this->addColumn('store_id', array(
            'header' => Mage::helper('adabra_feed')->__('Store'),
            'index' => 'store_id',
            'type' => 'options',
            'options' => Mage::getSingleton('adabra_feed/source_store')->toHashArray(),
            'frame_callback' => array($this, 'decorateStore'),
        ));

        $this->addColumn('enabled', array(
            'header' => Mage::helper('adabra_feed')->__('Enabled'),
            'align' => 'left',
            'width' => '120',
            'index' => 'enabled',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('adabra_feed')->__('Disabled'),
                1 => Mage::helper('adabra_feed')->__('Enabled'),
            ),
        ));
        
        $this->addColumn('currency', array(
            'header' => Mage::helper('adabra_feed')->__('Currency'),
            'align' => 'left',
            'width' => '120',
            'index' => 'currency',
            'type' => 'text',
        ));

        $types = Mage::getSingleton('adabra_feed/source_type')->toHashArray();
        foreach ($types as $type => $desc) {
            $this->addColumn('status_'.$type, array(
                'header' => Mage::helper('adabra_feed')->__($desc),
                'index' => 'status_'.$type,
                'type' => 'options',
                'width' => '120px',
                'options' => Mage::getSingleton('adabra_feed/source_status')->toHashArray(),
                'frame_callback' => array($this, 'decorateStatus'),
            ));
        }

        $this->addColumn('updated_at', array(
            'header' => $this->__('Update time'),
            'width' => '180',
            'index' => 'updated_at',
            'filter' => false,
            'align' => 'left',
            'frame_callback' => array($this, 'decorateDate')
        ));

        $this->addColumn('action', array(
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
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId(),
        ));
    }
}
