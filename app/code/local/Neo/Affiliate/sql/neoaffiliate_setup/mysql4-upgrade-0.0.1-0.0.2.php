<?php
    $installer = $this;
	$installer->startSetup();
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	
	$entityTypeId = $setup->getEntityTypeId('customer');
	$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
	$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

	// new attribute name is 'repcode'
	// Type varchar
	$installer->addAttribute("customer","repcode",array(
		"type" => "varchar",
		"backend" => "",
		"label" => "Rep Code",
		"input" => "text",
		"visible" => true,
		"required" => false,
		"default" => null,
		"frontend" => "",
		"unique" => false,
		"visible_on_front" => true,
		"note" => "Affiliate Repcode"
	));
	
	$attribute = Mage::getSingleton("eav/config")->getAttribute("customer","repcode");
	
	$setup->addAttributeToGroup(
	    $entityTypeId,
	    $attributeSetId,
	    $attributeGroupId,
	    'repcode',
	    '1000'
	);
	
	$used_in_forms = array();
	
	$used_in_forms[] = "adminhtml_customer";
	
	$attribute->setData("used_in_forms",$used_in_forms)
			->setData("is_used_for_customer_segment",true)
			->setData("is_system",0)
			->setData("is_user_defined",1)
			->setData("is_visible",1)
			->setData("sort_order",100);

	$attribute->save();
	
	$installer->endSetup();
?>