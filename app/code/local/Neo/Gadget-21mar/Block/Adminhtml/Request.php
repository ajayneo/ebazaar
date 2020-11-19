<?php


class Neo_Gadget_Block_Adminhtml_Request extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

		$this->_controller = "adminhtml_request";
		$this->_blockGroup = "gadget";
		$this->_headerText = Mage::helper("gadget")->__("Request Manager");
		$this->_addButtonLabel = Mage::helper("gadget")->__("Add New Item");
		parent::__construct();
		$this->_removeButton('add');
	}

}