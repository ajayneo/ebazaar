<?php

class Creatuitycorp_Shareyourpurchase_Block_Adminhtml_Statistics_Grid 
    extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('statistics_grid');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection() {
        $collection = Mage::getModel('shareyourpurchase/statistics')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('shareyourpurchase')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));
        
        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('shareyourpurchase')->__('Order #'),
            'align' => 'left',
            'index' => 'order_increment_id',
        ));
        
        $this->addColumn('product_name', array(
            'header' => Mage::helper('shareyourpurchase')->__('Product Name'),
            'align' => 'left',
            'index' => 'product_name',
        ));
        
        $this->addColumn('provider', array(
            'header' => Mage::helper('shareyourpurchase')->__('Provider'),
            'align' => 'left',
            'index' => 'provider',
        ));
        
        $this->addColumn('shared_at', array(
            'header' => Mage::helper('shareyourpurchase')->__('Shared At'),
            'align' => 'left',
            'index' => 'shared_at',
            'type' => 'datetime',
        ));
        
        return parent::_prepareColumns();
    }
    
    public function _prepareMassAction() {
        $this->setMassactionIdField('statistic_enitity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('shareyourpurchase')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('shareyourpurchase')->__('Are you sure?')
        ));
        return $this;
    }
    
}
