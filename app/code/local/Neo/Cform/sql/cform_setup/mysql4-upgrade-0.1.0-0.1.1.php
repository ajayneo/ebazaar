<?php
    //Attribute to add
	$newAttributeName = "cform"; //modify this with the name of your attribute
 
	//a) Add EAV Attributes (modify as you needed)
	$attribute = array(
	    'type' => 'varchar',
	    'label' => 'CForm',
	    'visible' => true,
	    'required' => false,
	    'user_defined' => false,
	    'searchable' => false,
	    'filterable' => false,
	    'comparable' => false,
	);
 
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	//Add to customer
	$setup->addAttribute('customer', $newAttributeName, $attribute);
	
	//b) Add Quote attributes (one page step to step save field)
	$setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
	$setup->getConnection()->addColumn(
	        $setup->getTable('sales_flat_quote'),
	        $newAttributeName,
	        'text NULL DEFAULT NULL'
	    );

	$setup->addAttribute('quote',"customer_cform",array('type' => 'varchar'));
?>