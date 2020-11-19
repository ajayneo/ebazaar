<?php
class Neo_Career_Block_Adminhtml_Career extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_career';
    $this->_blockGroup = 'career';
    $this->_headerText = Mage::helper('career')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('career')->__('Add Item');
    parent::__construct();
  }
}