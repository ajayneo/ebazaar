<?php
    /* @var $installer Mage_Sales_Model_Resource_Setup */
    $installer = Mage::getResourceModel('sales/setup','sales_setup');
	
	$installer->startSetup();
	
	$installer->addAttribute('order','repcode',array(
		'label' => 'Rep Code',
		'type' => 'varchar',
		'input' => 'text',
		'required' => false,
	));
	
	$installer->endSetup();
?>