<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('adabra_feed/feed');

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('adabra_feed_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identify' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true), 'Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Store ID')
    ->addColumn('enabled', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
    ), 'Enabled')
    ->addColumn('currency', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Currency')
    ->addColumn('status_order', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Status order')
    ->addColumn('status_product', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Status product')
    ->addColumn('status_category', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Status category')
    ->addColumn('status_customer', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Status customer')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ), 'Updated at')

    ->addIndex(
        $installer->getIdxName(
            'adabra_feed/feed',
            array('currency', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array(
            array('name' => 'currency', 'size' => 128),
            'store_id',
        ),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )

    ->addForeignKey(
        $this->getFkName($this->getTable('adabra_feed/feed'), 'store_id', 'core/store', 'store_id'),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->setOption('type', 'Innodb')
    ->setComment('Adabra Feed');

$installer->getConnection()->createTable($table);

$tableName = $installer->getTable('adabra_feed/vfield');

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('adabra_feed_vfield_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identify' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true), 'Id')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Field Code')
    ->addColumn('mode', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Mode')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Value')

    ->addIndex(
        $installer->getIdxName(
            'adabra_feed/vfield',
            array('code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array(
            array('name' => 'code', 'size' => 128),
        ),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )

    ->setOption('type', 'Innodb')
    ->setComment('Adabra Virtual Fields');

$installer->getConnection()->createTable($table);
$installer->endSetup();
