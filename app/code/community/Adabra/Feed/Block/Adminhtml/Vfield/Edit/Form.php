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

class Adabra_Feed_Block_Adminhtml_Vfield_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('adminhtml/adabra_vfield/save', array(
                '_current' => true,
            )),
            'method' => 'post',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'general',
            array('legend' => $this->__('Virtual Field'))
        );

        $fieldset->addField('code', 'label', array(
            'label'     => Mage::helper('adabra_feed')->__('Code'),
            'name'      => 'code',
            'class'     => 'required-entry',
        ));

        $fieldset->addField('mode', 'select', array(
            'label'     => Mage::helper('adabra_feed')->__('Mode'),
            'name'      => 'mode',
            'class'     => 'required-entry',
            'values'    => Mage::getSingleton('adabra_feed/source_vfield_mode')->toOptionArray(),
        ));

        $fieldset->addField('value', 'text', array(
            'label'     => Mage::helper('adabra_feed')->__('Value'),
            'name'      => 'value',
            'class'     => 'required-entry',
        ));

        if (Mage::registry('adabra_vfield_data') && Mage::registry('adabra_vfield_data')->getData()) {
            $data = Mage::registry('adabra_vfield_data')->getData();
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }
}
