<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("customer","affiliate_store",array(
    "type" => "varchar",
    "backend" => "",
    "label" => "Affiliate Store Name",
    "input" => "text",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false,
    "visible_on_front" => true,
    "note" => "Affiliate Store Name"
));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer","affiliate_store");

$used_in_forms=array();

$used_in_forms[]="adminhtml_customer";

$attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 100);
		
$attribute->save();

$installer->endSetup();