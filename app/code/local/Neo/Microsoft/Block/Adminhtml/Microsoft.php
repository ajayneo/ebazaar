<?php


class Neo_Microsoft_Block_Adminhtml_Microsoft extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_microsoft";
	$this->_blockGroup = "microsoft";
	$this->_headerText = Mage::helper("microsoft")->__("Microsoft Manager");
	$this->_addButtonLabel = Mage::helper("microsoft")->__("Add New Item");
	parent::__construct();
	
	}

}