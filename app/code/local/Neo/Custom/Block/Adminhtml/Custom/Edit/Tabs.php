<?php

class Neo_Custom_Block_Adminhtml_Custom_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('custom_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('custom')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('custom')->__('Item Information'),
          'title'     => Mage::helper('custom')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('custom/adminhtml_custom_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}