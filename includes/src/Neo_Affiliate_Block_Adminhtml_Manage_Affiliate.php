<?php
    class Neo_Affiliate_Block_Adminhtml_Manage_Affiliate extends Mage_Adminhtml_Block_Widget_Grid_Container {
    	
		public function __construct()
		{
			$this->_blockGroup = 'neo_affiliate';
			$this->_controller = 'adminhtml_manage_affiliate';
			$this->_headerText = Mage::helper('adminhtml')->__('Manage Affiliate');
			
			parent::__construct();
			$this->_removeButton('add');
		}
		
		/*public function getOrderProductsDetails($id)
		{
			$affiliate_model = Mage::getModel('neoaffiliate/affiliate')->load($id);
			$order_model = Mage::getModel('sales/order')->load($affiliate_model->getOrderId());
			return $order_model->getAllItems();
		}*/
    }
?>