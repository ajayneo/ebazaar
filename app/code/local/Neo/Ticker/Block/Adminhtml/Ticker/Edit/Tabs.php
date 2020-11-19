<?php

class Neo_Ticker_Block_Adminhtml_Ticker_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('ticker_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('ticker')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('ticker')->__('Item Information'),
          'title'     => Mage::helper('ticker')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('ticker/adminhtml_ticker_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}