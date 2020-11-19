<?php
	$installer = $this;
	$installer->startSetup();
	$this->addAttribute('customer','mobile',array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'Mobile',
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
	
	$customerattribute = Mage::getModel('customer/attribute')->loadByCode('customer','mobile');
	$forms=array('customer_account_edit','customer_account_create','adminhtml_customer','checkout_register');
	$customerattribute->setData('used_in_forms',$forms);
	$customerattribute->save();
	
	/*if(version_compare(Mage::getVersion(),'1.6.0','<='))
	{
		$customer = Mage::getModel('customer/customer');
		$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
		$this->addAttributeToSet('customer',$attrSetId,'General','mobile');
	}
	
	if(version_compare(Mage::getVersion(), '1.4.2', '>='))
	{
		Mage::getSingleton('eav/config')->getAttribute('customer','mobile')->setData('used_in_forms',array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))->save();
	}*/
?>