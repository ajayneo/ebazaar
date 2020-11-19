<?php


class Neo_Productpricereport_Block_Adminhtml_Productpricereport extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_productpricereport";
	$this->_blockGroup = "productpricereport";
	$this->_headerText = Mage::helper("productpricereport")->__("Productpricereport Manager");
	$this->_addButtonLabel = Mage::helper("productpricereport")->__("Add New Item");
	parent::__construct();
	
	}

}