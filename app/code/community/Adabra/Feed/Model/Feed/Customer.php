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

class Adabra_Feed_Model_Feed_Customer extends Adabra_Feed_Model_Feed_Abstract
{
    protected $_type = 'customer';
    protected $_exportName = 'customers';
    protected $_scope = 'website';
    protected $_virtualFields = array();


    /**
     * Get virtual field model
     * @param $fieldName
     * @return Adabra_Feed_Model_Source_Vfield
     */
    protected function _getVirtualFieldModel($fieldName)
    {
        if (!isset($this->_virtualFields[$fieldName])) {
            $this->_virtualFields[$fieldName] = Mage::getModel('adabra_feed/vfield')
                ->getCollection()
                ->addFieldToFilter('vfield_type', ['eq' => Adabra_Feed_Model_Source_Vfield_Type::TYPE_CUSTOMER])
                ->addFieldToFilter('code', ['eq' => $fieldName])
                ->getFirstItem();
        }

        return $this->_virtualFields[$fieldName];
    }

    /**
     * Get virtual field
     * @param Mage_Customer_Model_Customer $customer
     * @param $fieldName
     * @return string
     */
    protected function _getVirtualField(Mage_Customer_Model_Customer $customer, $fieldName)
    {
        $fieldModel = $this->_getVirtualFieldModel($fieldName);
        return $fieldModel->getCustomerValue($customer);
    }

    /**
     * Return true if $customer is a newsletter subscirber
     * @param Mage_Customer_Model_Customer $customer
     * @return boolean
     */
    protected function _getIsNewsletterSubscriber(Mage_Customer_Model_Customer $customer)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $tableName = $readConnection->getTableName('newsletter_subscriber');
        $qry = $readConnection->select()->from($tableName, 'subscriber_id')
            ->where('customer_id = ' . $readConnection->quote($customer->getId()))
            ->where('subscriber_status = 1')
            ->limit(1);

        return $readConnection->fetchOne($qry) ? true : false;
    }

    /**
     * Prepare collection
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $this->_collection = Mage::getModel('customer/customer')->getCollection();
        $this->_collection
            ->addAttributeToSelect('*')
            ->addFieldToFilter('website_id', $this->getStore()->getWebsiteId());
    }

    /**
     * Get headers
     * @return array
     */
    protected function _getHeaders()
    {
        return array(
            'id_utente',
            'email',
            'nome',
            'cognome',
            'citta',
            'cap',
            'indirizzo',
            'provincia',
            'regione',
            'stato',
            'cellulare',
            'telefono',
            'sesso',
            'nascita_anno',
            'nascita_mese',
            'nascita_giorno',
            'f_business',
            'azienda_nome',
            'azienda_categoria',
            'f_ricevi_newsletter',
            'f_ricevi_comunicazioni_commerciali',
            'data_iscrizione',
            'data_cancellazione',
            'ip',
            'user_agent',
            'f_attivo',
            'f_cancellato',
            'fidelity_card',
        );
    }

    /**
     * Get feed row
     * @param Varien_Object $entity
     * @return array
     */
    protected function _getFeedRow(Varien_Object $entity)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $entity;

        $shippingAddress = $customer->getDefaultShippingAddress();
        $billingAddress = $customer->getDefaultBillingAddress();

        $dob = preg_split('/\D+/', $customer->getDob());

        if (!isset($dob[0])) {
            $dob[0] = '';
        }

        if (!isset($dob[1])) {
            $dob[1] = '';
        }

        if (!isset($dob[2])) {
            $dob[2] = '';
        }

        return array(array(
            $customer->getId(),
            $customer->getEmail(),
            $customer->getFirstname(),
            $customer->getLastname(),
            $shippingAddress ? $shippingAddress->getCity() : '',
            $shippingAddress ? $shippingAddress->getPostcode() : '',
            $shippingAddress ? $shippingAddress->getStreetFull() : '',
            '',
            $shippingAddress ? $shippingAddress->getRegionCode() : '',
            $shippingAddress ? $shippingAddress->getCountry() : '',
            '',
            $shippingAddress ? $shippingAddress->getTelephone() : '',
            $customer->getGender() == 2 ? 'f' : 'm',
            $dob[0],
            $dob[1],
            $dob[2],
            $this->_getVirtualField($customer, 'f_business'),
            $billingAddress ? $billingAddress->getCompany() : '',
            $this->_getVirtualField($customer, 'azienda_categoria'),
            $this->_toBoolean($this->_getIsNewsletterSubscriber($customer)),
            $this->_toBoolean($this->_getVirtualField($customer, 'f_ricevi_comunicazioni_commerciali')),
            $this->_toTimestamp2($customer->getCreatedAtTimestamp()),
            '',
            '',
            '',
            $this->_toBoolean(true),
            $this->_toBoolean(false),
            $this->_getVirtualField($customer, 'fidelity_card'),
        ));
    }
}
