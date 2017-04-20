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

class Adabra_Feed_Block_Adminhtml_Feed_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('adminhtml/adabra_feed/save', array(
                '_current' => true,
            )),
            'method' => 'post',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'general',
            array('legend' => $this->__('Feed Details'))
        );

        $fieldset->addField('store_id', 'select', array(
            'label'     => Mage::helper('adabra_feed')->__('Store'),
            'name'      => 'store_id',
            'class'     => 'required-entry',
            'values'    => Mage::getSingleton('adabra_feed/source_store')->toOptionArray(),
        ));
        $fieldset->addField('currency', 'select', array(
            'label'     => Mage::helper('adabra_feed')->__('Currency'),
            'name'      => 'currency',
            'class'     => 'required-entry',
            'values'    => Mage::getSingleton('adabra_feed/source_currency')->toOptionArray(),
        ));
        $fieldset->addField('enabled', 'select', array(
            'label'     => Mage::helper('adabra_feed')->__('Enabled'),
            'name'      => 'enabled',
            'class'     => 'required-entry',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $feed = Mage::registry('adabra_feed_data');
        if ($feed && $feed->getData()) {
            $data = $feed->getData();

            if ($feed->getId()) {
                $fieldset->addField('code', 'label', array(
                    'label'     => Mage::helper('adabra_feed')->__('Code'),
                    'name'      => 'code',
                    'class'     => 'required-entry',
                ));
                $data['code'] = $feed->getCode();

                $types = Mage::getSingleton('adabra_feed/source_type')->toHashArray();
                foreach ($types as $type => $label) {
                    $fieldset->addField('url_'.$type, 'label', array(
                        'label'     => Mage::helper('adabra_feed')->__('Feed URL %s', $label),
                        'name'      => 'url_'.$type,
                        'class'     => 'required-entry',
                    ));

                    $data['url_'.$type] = $feed->getFeedTypeInstance($type)->getUrl();
                }
            }

            $form->setValues($data);
        }

        return parent::_prepareForm();
    }
}
