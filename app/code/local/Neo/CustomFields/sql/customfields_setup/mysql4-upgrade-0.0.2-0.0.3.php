<?php
	$installer = $this;
	$installer->startSetup();
	$this->addAttribute('customer','cus_country',array(
		'type' => 'varchar',
		'input' => 'select',
		'label' => 'Country',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'position'=>1,
		'sort_order' => 80,
		'source' => 'customfields/selectoptions',
		'user_defined' => 1, 
		'default' => null,
		'visible_on_front' => 1 
	));
	
	$installer->endSetup();
	
	$customerattribute = Mage::getModel('customer/attribute')->loadByCode('customer','cus_country');
	$forms=array('customer_account_edit','customer_account_create','adminhtml_customer','checkout_register');
	$customerattribute->setData('used_in_forms',$forms);
	$customerattribute->save();
?>