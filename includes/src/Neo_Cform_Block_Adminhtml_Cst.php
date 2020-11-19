<?php


class Neo_Cform_Block_Adminhtml_Cst extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_cst";
	$this->_blockGroup = "cform";
	$this->_headerText = Mage::helper("cform")->__("Cst Manager");
	$this->_addButtonLabel = Mage::helper("cform")->__("Add New Item");
	parent::__construct();
	
	}

}