<?php


class Neo_Easyfinance_Block_Adminhtml_Easyfinance extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_easyfinance";
	$this->_blockGroup = "easyfinance";
	$this->_headerText = Mage::helper("easyfinance")->__("Easyfinance Manager");
	$this->_addButtonLabel = Mage::helper("easyfinance")->__("Add New Item");
	parent::__construct();
	
	}

}