<?php


class Neo_Ebautomation_Block_Adminhtml_Priceupdatelog extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{
		$this->_controller = "adminhtml_priceupdatelog";
		$this->_blockGroup = "ebautomation";
		$this->_headerText = Mage::helper("ebautomation")->__("Priceupdatelog Manager");
		$this->_addButtonLabel = Mage::helper("ebautomation")->__("Add New Item");
		parent::__construct();
		$this->_removeButton('add');
	}

}