<?php


class Neo_Supersale_Block_Adminhtml_Super extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_super";
	$this->_blockGroup = "supersale";
	$this->_headerText = Mage::helper("supersale")->__("Super Manager");
	$this->_addButtonLabel = Mage::helper("supersale")->__("Add New Item");
	parent::__construct();
	
	}

}