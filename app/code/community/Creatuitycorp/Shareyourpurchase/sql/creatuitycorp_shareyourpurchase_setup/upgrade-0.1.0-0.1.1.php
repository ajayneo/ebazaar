<?php
 /**
 * Instal/Upgrade Script
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
$installer = $this;
/* @var installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$table = $installer->getConnection()
        ->newTable($installer->getTable('shareyourpurchase/statistics'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
            'unsigned' => true,
        ), 'Entity ID')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => true,
        ), 'Order ID')
        ->addColumn('provider', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
            'nullable' => false,
        ), 'Provider')
        ->addColumn('shared_at', Varien_Db_Ddl_Table::TYPE_DATETIME)
        ->addIndex('IDX_SHAREYOURPURCHASE_STATISTICS_ORDER_ID', array('order_id'))
        ->addForeignKey('FK_SHAREYOURPURCHASE_STATISTICS_ORDER_ID', 
                'order_id', 
                $installer->getTable('sales/order'), 
                'entity_id', 
                Varien_Db_Ddl_Table::ACTION_SET_NULL,
                Varien_Db_Ddl_Table::ACTION_SET_NULL)
        ->setComment('Shareyourpurchase Statistics Table');

$installer->getConnection()->createTable($table);

$installer->endSetup();

