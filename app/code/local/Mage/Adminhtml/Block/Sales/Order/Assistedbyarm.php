<?php
class Mage_Adminhtml_Block_Sales_Order_Assistedbyarm
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$increment_id = $row->getData('increment_id');
        // print_r($row->getData());
        $_order = Mage::getModel('sales/order')->getCollection();
        $_order->addFieldToFilter('increment_id',$increment_id);
        $_order->addFieldToSelect('assisted_by_arm');
        $arm = $_order->getColumnValues('assisted_by_arm');
        // print_r($arm);
        // echo count($arm);
        // var_dump($arm[0]);
        $value = '';
        if($arm[0] !== NULL){
            if($arm[0] == 1){
                $value = 'Yes';
            }else{
                $value = 'No';
            }   
        }

        return $value;
        // if($arm[0] == 1){
        //     return 'Yes';
        // }else if($arm[0] == 0){
        //     return 'No';
        // }else{
        //     return '';
        // }
    	// $customer = Mage::getModel('customer/customer')->load($customer_id);

    	// if($this->getColumn()->getFilterName() == 'cust_map_status'){
    	// 	return $customer->getNavMapStatus()?'Yes':'No';
    	// }
    	// if($this->getColumn()->getFilterName() == 'customer_email'){
    	// 	return $customer->getEmail();
    	// }
       
    }
}


?>