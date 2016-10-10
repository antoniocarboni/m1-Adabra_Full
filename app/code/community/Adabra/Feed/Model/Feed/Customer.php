<?php
class Adabra_Feed_Model_Feed_Customer extends Adabra_Feed_Model_Feed_Abstract
{
    protected $_type = 'customer';
    protected $_exportName = 'customers';
    protected $_scope = 'website';

    /**
     * Get virtual field
     * @param Mage_Customer_Model_Customer $customer
     * @param $fieldName
     * @return string
     */
    protected function _getVirtualField(Mage_Customer_Model_Customer $customer, $fieldName)
    {
        return '';
    }

    /**
     * Return true if $customer is a newsletter subscirber
     * @param Mage_Customer_Model_Customer $customer
     * @return boolean
     */
    protected function _getIsNewsletterSubscriber(Mage_Customer_Model_Customer $customer)
    {
        $subscriber = Mage::getSingleton('newsletter/subscriber');
        $subscriber->clearInstance()->loadByEmail($customer->getEmail());

        $res = $subscriber->getId() ? true : false;
        $subscriber->clearInstance();
        return $res;
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
        ));
    }
}
