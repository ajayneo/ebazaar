<?php

class Neo_Ebautomation_Block_Adminhtml_Catalog_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    private $parent;

    protected function _prepareLayout()
    {
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        //add new tab

       //echo $url = Mage::helper('adminhtml')->getUrl("admin_ebautomation/adminhtml_priceupdatelog/index", array('_current' => true));

        $this->addTab('pricelog', array(
                     'label'     => Mage::helper('catalog')->__('Price Update Log'),
                     'content'   => $this->getLayout()->createBlock('ebautomation/adminhtml_priceupdatelog','pricelog')->toHtml(),
                     'title'     => Mage::helper('catalog')->__('Price Update Log'),
                     /*'url'       => $url,
                     'class'     => 'ajax', */ // load the frid via ajax
        ));
        return $this;
    }
}