<?php


class Neo_Affiliatelimit_Block_Adminhtml_Limit extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_limit";
	$this->_blockGroup = "affiliatelimit";
	$this->_headerText = Mage::helper("affiliatelimit")->__("Limit Manager");
	$this->_addButtonLabel = Mage::helper("affiliatelimit")->__("Add New Item");
	parent::__construct();
	
	}

}