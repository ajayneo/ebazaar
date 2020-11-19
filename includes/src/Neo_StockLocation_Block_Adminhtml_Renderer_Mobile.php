<?php
class Neo_StockLocation_Block_Adminhtml_Renderer_Mobile extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {                    
        $order = Mage::getModel('sales/order')->load($row['entity_id']);              
        
        $result = '';

        if(strlen($order->getRemoteIp()) > 0) { 
            $result = 'Website';
        } else {
            $result = 'Application';
        }
        
        
        
        unset($order);
        return $result;
    }
}