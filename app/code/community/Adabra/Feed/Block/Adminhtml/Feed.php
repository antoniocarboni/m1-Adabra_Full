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
