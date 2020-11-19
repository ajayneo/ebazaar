<?php


class Neo_Suvidha_Block_Adminhtml_Creditsuvidha extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_creditsuvidha";
	$this->_blockGroup = "suvidha";
	$this->_headerText = Mage::helper("suvidha")->__("Creditsuvidha Manager");
	$this->_addButtonLabel = Mage::helper("suvidha")->__("Add New Item");
	parent::__construct();
	
	}

}