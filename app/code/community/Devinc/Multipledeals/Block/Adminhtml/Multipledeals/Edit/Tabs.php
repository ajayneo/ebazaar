<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('multipledeals_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('multipledeals')->__('Deal Information'));
  }
  
  protected function _prepareLayout()
  {	  
      $this->addTab('settings_section', array(
          'label'     => Mage::helper('multipledeals')->__('Deal Settings'),
          'title'     => Mage::helper('multipledeals')->__('Deal Settings'),
          'content'   => Mage::getModel('license/module')->getDealSettingsBlock($this, 'multipledeals'),
      ));
	  
      $this->addTab('products_section', array(
          'label'     => Mage::helper('multipledeals')->__('Select a Product'),
          'title'     => Mage::helper('multipledeals')->__('Select a Product'),
          'content'   => Mage::getModel('license/module')->getProductsBlock($this, 'multipledeals'),
      ));
     
      return parent::_prepareLayout();
  }
}