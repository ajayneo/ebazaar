<?php


class Neo_Asmdetail_Block_Adminhtml_Asm extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_asm";
	$this->_blockGroup = "asmdetail";
	$this->_headerText = Mage::helper("asmdetail")->__("Asm Manager");
	$this->_addButtonLabel = Mage::helper("asmdetail")->__("Add New Item");
	parent::__construct();
	
	}

}