<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$tableName = $installer->getTable('adabra_feed/feed');

$installer->getConnection()
    ->addColumn($tableName, 'vfield_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'vField Type');

$installer->endSetup();