<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$tableName = $installer->getTable('adabra_feed/feed');

$installer->getConnection()
    ->addColumn($tableName, 'adabra_catalog_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'comment' => 'Catalog ID'
    ));

$installer->endSetup();