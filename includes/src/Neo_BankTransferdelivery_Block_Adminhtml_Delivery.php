<?php


class Neo_BankTransferdelivery_Block_Adminhtml_Delivery extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_delivery";
	$this->_blockGroup = "banktransferdelivery";
	$this->_headerText = Mage::helper("banktransferdelivery")->__("Delivery Manager");
	$this->_addButtonLabel = Mage::helper("banktransferdelivery")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}