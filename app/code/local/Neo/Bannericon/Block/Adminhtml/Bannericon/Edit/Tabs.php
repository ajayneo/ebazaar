<?php

class Neo_Bannericon_Block_Adminhtml_Bannericon_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('bannericon_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('bannericon')->__('Icon Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('bannericon')->__('Icon Information'),
          'title'     => Mage::helper('bannericon')->__('Icon Information'),
          'content'   => $this->getLayout()->createBlock('bannericon/adminhtml_bannericon_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}