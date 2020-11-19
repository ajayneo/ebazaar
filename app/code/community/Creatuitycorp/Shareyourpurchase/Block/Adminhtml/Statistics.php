<?php

class Creatuitycorp_Shareyourpurchase_Block_Adminhtml_Statistics 
    extends Mage_Adminhtml_Block_Widget_Grid_Container {
    
    public function __construct() {
        $this->_callGrandParentConstruct();
        $this->setTemplate('widget/grid/container.phtml');
    }
    
    protected function _callGrandParentConstruct() {
        $grandParentClass = get_parent_class(get_parent_class($this));
        $callback = array($this, $grandParentClass . '::' . '__construct');
        return call_user_func($callback);
    }
    
    protected function _construct() {
        
        $this->_controller = 'adminhtml_statistics';
        $this->_blockGroup = 'shareyourpurchase';
        $this->_headerText = Mage::helper('shareyourpurchase')->__('Statistics');
        parent::_construct();
    }
    
}
