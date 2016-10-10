<?php
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
