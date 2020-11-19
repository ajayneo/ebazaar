<?php


class Neo_Affiliatecommision_Block_Adminhtml_Commision extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_commision";
	$this->_blockGroup = "affiliatecommision";
	$this->_headerText = Mage::helper("affiliatecommision")->__("Commision Manager");
	$this->_addButtonLabel = Mage::helper("affiliatecommision")->__("Add New Item");
	parent::__construct();
	
	}

}