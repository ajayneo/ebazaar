<?php


class Neo_Offers_Block_Adminhtml_Offers extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_offers";
	$this->_blockGroup = "offers";
	$this->_headerText = Mage::helper("offers")->__("Offers Manager");
	$this->_addButtonLabel = Mage::helper("offers")->__("Add New Item");
	parent::__construct();
	
	}

}