<?php


class Neo_Orderreturn_Block_Adminhtml_Return extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_return";
	$this->_blockGroup = "orderreturn";
	$this->_headerText = Mage::helper("orderreturn")->__("Return Manager");
	$this->_addButtonLabel = Mage::helper("orderreturn")->__("Add New Item");
	parent::__construct();
	
	}

}