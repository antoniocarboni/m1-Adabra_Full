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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// Fill feeds
$tableName = $installer->getTable('adabra_feed/feed');
$stores = Mage::getModel('core/store')->getCollection();
foreach ($stores as $store) {
    $installer->getConnection()->insert($tableName, array(
        'store_id' => $store->getId(),
        'currency' => $store->getBaseCurrencyCode(),
        'enabled' => '1',
        'status_order' => Adabra_Feed_Model_Source_Status::MARKED_REBUILD,
        'status_product' => Adabra_Feed_Model_Source_Status::MARKED_REBUILD,
        'status_category' => Adabra_Feed_Model_Source_Status::MARKED_REBUILD,
        'status_customer' => Adabra_Feed_Model_Source_Status::MARKED_REBUILD,
        'updated_at' => new Zend_Db_Expr('NOW()'),
    ));
}

// Fill v-fields
$tableName = $installer->getTable('adabra_feed/vfield');
$vFields = Mage::getSingleton('adabra_feed/source_vfield')->toArray();
foreach ($vFields as $vField) {
    $installer->getConnection()->insert($tableName, array(
        'code' => $vField,
        'mode' => 'map',
        'value' => strtolower($vField),
    ));
}

$installer->endSetup();
