<?php


class Neo_Asmdetail_Block_Adminhtml_Asmdetail extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_asmdetail";
	$this->_blockGroup = "asmdetail";
	$this->_headerText = Mage::helper("asmdetail")->__("ASM Detail Manager");
	$this->_addButtonLabel = Mage::helper("asmdetail")->__("Add New ASM Detail");
	parent::__construct();
	
	}

}