<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$tableName = $installer->getTable('adabra_feed/vfield');

$installer->getConnection()
    ->addColumn($tableName, 'vfield_type', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'size' => null,
        'nullable' => false,
        'comment' => 'vField Type'
    ));

$installer->endSetup();