<?php
class Mage_Adminhtml_Block_Sales_Order_Customerdata
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$customer_id = $row->getData('customer_id');
    	$customer = Mage::getModel('customer/customer')->load($customer_id);

    	if($this->getColumn()->getFilterName() == 'cust_map_status'){
    		return $customer->getNavMapStatus()?'Yes':'No';
    	}
    	if($this->getColumn()->getFilterName() == 'customer_email'){
    		return $customer->getEmail();
    	}
       
    }
}


?>