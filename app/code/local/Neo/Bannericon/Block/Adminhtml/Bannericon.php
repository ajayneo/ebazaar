<?php
class Neo_Bannericon_Block_Adminhtml_Bannericon extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_bannericon';
    $this->_blockGroup = 'bannericon';
    $this->_headerText = Mage::helper('bannericon')->__('Banner Icon Manager');
    $this->_addButtonLabel = Mage::helper('bannericon')->__('Add Icon');
    parent::__construct();
  }
}