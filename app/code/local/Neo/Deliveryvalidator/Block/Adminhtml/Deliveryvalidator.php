<?php


class Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_deliveryvalidator";
	$this->_blockGroup = "deliveryvalidator";
	$this->_headerText = Mage::helper("deliveryvalidator")->__("Deliveryvalidator Manager");
	$this->_addButtonLabel = Mage::helper("deliveryvalidator")->__("Add New Item");
	parent::__construct();
	
	}

}