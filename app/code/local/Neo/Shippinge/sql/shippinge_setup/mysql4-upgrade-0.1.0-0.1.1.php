<?php
    $installer = $this;
	$installer->startSetup();
	Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
	$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
	$attribute = array(
		'type' => 'varchar',
		'backend_type' => 'varchar',
    	'frontend_input' => 'varchar',
    	'is_user_defined' => true,
    	'label' => 'Track Status Code',
	    'visible' => true,
	    'required' => false,
	    'user_defined' => false,
	    'default' => '0',
	    'comparable' => false,
	    'searchable' => false,
	    'filterable' => false
	);
	
	$installer->addAttribute('order','trackstatuscode',$attribute);
	$installer->endSetup();
?>