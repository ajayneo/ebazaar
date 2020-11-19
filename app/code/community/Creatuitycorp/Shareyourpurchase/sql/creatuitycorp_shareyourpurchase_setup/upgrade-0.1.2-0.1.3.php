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

$installer->getConnection()->addColumn(
        $this->getTable('shareyourpurchase/statistics'),
        'product_name',
        'varchar(255) default NULL'
);


$installer->endSetup();

