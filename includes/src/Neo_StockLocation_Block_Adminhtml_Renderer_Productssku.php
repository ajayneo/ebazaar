<?php
class Neo_StockLocation_Block_Adminhtml_Renderer_Productssku extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {       
        //$getData = $row->getData();               
        $order = Mage::getModel('sales/order')->load($row['entity_id']);              
  

        $result = "<ul>";
 
        foreach($order->getAllItems() as $_order)
        {

            $result .= "<li>".$_order->getSku()."</li>";

        }
      
          
        $result .= "</ul>";
        
        
        
        unset($order);
        return $result;
    }
}