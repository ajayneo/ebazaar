<?php


class Neo_Gadget_Block_Adminhtml_Gadget extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_gadget";
	$this->_blockGroup = "gadget";
	$this->_headerText = Mage::helper("gadget")->__("Gadget Manager");
	$this->_addButtonLabel = Mage::helper("gadget")->__("Add New Item");
	parent::__construct();
	
	}

}