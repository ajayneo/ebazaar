<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Bannerslider
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('bannerslider_banner'),
    'iconid',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
		'length' => 11,
        'nullable' => true,
        'default' => 0,
        'comment' => 'Store Banner Icon Id'
    )
);
$installer->getConnection()->addColumn($installer->getTable('bannerslider_banner'),
    'iconcontent',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
		'length' => 255,
        'nullable' => true,
        'default' => '',
        'comment' => 'Store Banner Icon Content'
    )
);
$installer->endSetup();

