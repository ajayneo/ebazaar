<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "cat_label",  array(
    "type"     => "int",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Category Image Label",
    "input"    => "select",
    "class"    => "",
    "source"   => "custombestseller/eav_entity_attribute_source_categoryoptions15156684960",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "Please Select",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "This will be used in frontend to show image label"

	));
$installer->endSetup();
	 