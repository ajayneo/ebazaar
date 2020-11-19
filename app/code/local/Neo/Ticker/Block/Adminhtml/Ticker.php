<?php
class Neo_Ticker_Block_Adminhtml_Ticker extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ticker';
    $this->_blockGroup = 'ticker';
    $this->_headerText = Mage::helper('ticker')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('ticker')->__('Add Item');
    parent::__construct();
  }
}