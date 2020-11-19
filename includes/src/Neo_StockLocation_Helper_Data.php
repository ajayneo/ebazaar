<?php
class Neo_StockLocation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStockLocationOptions()
    {
        return array(
            ''                              => Mage::helper('sales')->__(''),
            'Kurla Warehouse'               => Mage::helper('sales')->__('Kurla Warehouse'),
            'Bhiwandi Warehouse'            => Mage::helper('sales')->__('Bhiwandi Warehouse'),
            'Bangalore YCH HUB'             => Mage::helper('sales')->__('Bangalore YCH HUB'),
            'Bangalore Proconnect HUB'      => Mage::helper('sales')->__('Bangalore Proconnect HUB'),
            'Andheri HO'                    => Mage::helper('sales')->__('Andheri HO'),
            'MP Warehouse'                  => Mage::helper('sales')->__('MP Warehouse'),
            'RJ Warehouse'                  => Mage::helper('sales')->__('RJ Warehouse'),
            'OR Warehouse'                  => Mage::helper('sales')->__('OR Warehouse'),
            'AS Warehouse'                  => Mage::helper('sales')->__('AS Warehouse'),
            'CG Warehouse'                  => Mage::helper('sales')->__('CG Warehouse'),
            'Hyderabad Warehouse'           => Mage::helper('sales')->__('Hyderabad Warehouse'),
            'Uttar Pradesh Warehouse'       => Mage::helper('sales')->__('Uttar Pradesh Warehouse'),
            'Tamilnadu Warehouse'           => Mage::helper('sales')->__('Tamilnadu Warehouse'),
            'Delhi Warehouse'               => Mage::helper('sales')->__('Delhi Warehouse') 
        );
    }
    
    public function checkStockLocationSaved()
    {
        $orderId = (Mage::app()->getRequest()->getParam('order_id', '') ? Mage::app()->getRequest()->getParam('order_id', '') : $_REQUEST['order_id']);            
        $stockLocationModel = Mage::getModel('stocklocation/location')->getCollection()->addFieldToSelect('stock_location')->addFieldToFilter('order_id',$orderId)->getFirstItem()->getData();

        if(isset($stockLocationModel['stock_location']) && !empty($stockLocationModel['stock_location']))
        {
            return $stockLocationModel['stock_location'];
        }
        else 
        {
            return false;
        }
    }
    
    public function returnLocation()
    {
        $orderId = (Mage::app()->getRequest()->getParam('order_id', '') ? Mage::app()->getRequest()->getParam('order_id', '') : 0);            
        
        if(empty($orderId) && $orderId == 0)
        {
            $invoicId = (Mage::app()->getRequest()->getParam('invoice_id', '') ? Mage::app()->getRequest()->getParam('invoice_id', '') : 0);            
            $shipId = (Mage::app()->getRequest()->getParam('shipment_id', '') ? Mage::app()->getRequest()->getParam('shipment_id', '') : 0);            
            
            if($invoicId > 0)
            {
                $invoice = Mage::getModel('sales/order_invoice')->load($invoicId);
                $orderId = $invoice->getOrderId();
            }
            elseif($shipId > 0)
            {
                $shipment = Mage::getModel('sales/order_shipment')->load($shipId);
                $orderId = $shipment->getOrderId();    
            }
            
        }
        
        $stockLocationModel = Mage::getModel('stocklocation/location')->getCollection()->addFieldToSelect('stock_location')->addFieldToFilter('order_id',$orderId)->getFirstItem()->getData();
        if(isset($stockLocationModel['stock_location']) && !empty($stockLocationModel['stock_location']))
        {
            return $stockLocationModel['stock_location'];  
        }
        else 
        {
            return "Stock Location Unknown"; 
        }
        
        
          
    }
}
	 