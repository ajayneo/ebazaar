<?php


class Neo_Vowdelight_Block_Adminhtml_Vowdelight extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_vowdelight";
	$this->_blockGroup = "vowdelight";
	$this->_headerText = Mage::helper("vowdelight")->__("Vowdelight Manager");
	$this->_addButtonLabel = Mage::helper("vowdelight")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}