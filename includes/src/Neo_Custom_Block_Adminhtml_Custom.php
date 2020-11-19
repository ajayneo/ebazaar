<?php
class Neo_Custom_Block_Adminhtml_Custom extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_custom';
    $this->_blockGroup = 'custom';
    $this->_headerText = Mage::helper('custom')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('custom')->__('Add Item');
    parent::__construct();
  }
}