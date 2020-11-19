<?php
	$installer = $this;
	$installer->startSetup();
	$this->addAttribute('customer','landmark',array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'Landmark',
		'visible' => 1,
		'required' => 0,
		'position'=>1,
		'sort_order'=>80,
		'global' => 1,
		'user_defined' => 1, 
		'default' => null,
		'visible_on_front' => 1 
	));
	
	$installer->endSetup();
	
	$customerattribute = Mage::getModel('customer/attribute')->loadByCode('customer','landmark');
	$forms=array('customer_account_edit','customer_account_create','adminhtml_customer','checkout_register');
	$customerattribute->setData('used_in_forms',$forms);
	$customerattribute->save();
?>