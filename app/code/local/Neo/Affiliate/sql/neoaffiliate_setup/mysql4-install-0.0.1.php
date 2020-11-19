<?php
    $installer = $this;
	$installer->startSetup();
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	
	$entityTypeId = $setup->getEntityTypeId('customer');
	$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
	$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

	// new attribute name is 'isaffiliate'
	// Type Integer
	$installer->addAttribute("customer","isaffiliate",array(
		"type" => "int",
		"backend" => "",
		"label" => "Is Affiliate",
		"input" => "select",
		'source' => 'eav/entity_attribute_source_boolean',
		"visible" => true,
		"required" => false,
		"default" => 0,
		"frontend" => "",
		"unique" => false,
		"note" => "To determine whether a customer is affiliate"
	));
	
	$attribute = Mage::getSingleton("eav/config")->getAttribute("customer","isaffiliate");
	
	$setup->addAttributeToGroup(
	    $entityTypeId,
	    $attributeSetId,
	    $attributeGroupId,
	    'isaffiliate',
	    '999'
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