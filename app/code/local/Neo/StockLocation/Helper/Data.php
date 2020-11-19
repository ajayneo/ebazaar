<?php
class Neo_StockLocation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStockLocationOptions()
    {
        $old_options = array(
            ''                              => Mage::helper('sales')->__(''),
            'Kurla Warehouse'               => Mage::helper('sales')->__('Kurla Warehouse'),
            'Bhiwandi Warehouse'            => Mage::helper('sales')->__('Bhiwandi Warehouse'),
            'Bangalore YCH HUB'             => Mage::helper('sales')->__('Bangalore YCH HUB'),
            'Bangalore Proconnect HUB'      => Mage::helper('sales')->__('Bangalore Proconnect HUB'),
            'Andheri HO'                    => Mage::helper('sales')->__('Andheri HO'),
            'MP Warehouse'                  => Mage::helper('sales')->__('MP Warehouse'),
            //'RJ Warehouse'                  => Mage::helper('sales')->__('RJ Warehouse'),
            'OR Warehouse'                  => Mage::helper('sales')->__('OR Warehouse'),
            'AS Warehouse'                  => Mage::helper('sales')->__('AS Warehouse'),
            'CG Warehouse'                  => Mage::helper('sales')->__('CG Warehouse'),
            'Hyderabad Warehouse'           => Mage::helper('sales')->__('Hyderabad Warehouse'),
            'Uttar Pradesh Warehouse'       => Mage::helper('sales')->__('Uttar Pradesh Warehouse'),
            'Tamilnadu Warehouse'           => Mage::helper('sales')->__('Tamilnadu Warehouse'),
            'Delhi Warehouse'               => Mage::helper('sales')->__('Delhi Warehouse'), 
            'Kolkata Warehouse'             => Mage::helper('sales')->__('Kolkata Warehouse') ,
            'Haryana Warehouse'             => Mage::helper('sales')->__('Haryana Warehouse'), 
            'Himachal Pradesh Warehouse'    => Mage::helper('sales')->__('Himachal Pradesh Warehouse'),
            'Bhiwandi EB-REPAIR CENTRE'     => Mage::helper('sales')->__('Bhiwandi EB-REPAIR CENTRE'), 
            'Bhiwandi EB-SERVICE CENTRE'    => Mage::helper('sales')->__('Bhiwandi EB-SERVICE CENTRE'), 
            'Bhiwandi EB-Open Units'        => Mage::helper('sales')->__('Bhiwandi EB-Open Units'), 
            'Bhiwandi EB-Warranty stock'    => Mage::helper('sales')->__('Bhiwandi EB-Warranty stock'), 
            'Bhiwandi EB-Refurbished'       => Mage::helper('sales')->__('Bhiwandi EB-Refurbished'),            
            'Amazon BB – ASIS-Bhiwandi'     => Mage::helper('sales')->__('Amazon BB – ASIS-Bhiwandi'), 
            'Amazon BB – QC-Bhiwandi'       => Mage::helper('sales')->__('Amazon BB – QC-Bhiwandi'), 
            'Amazon BB – ASIS-Bangalore'    => Mage::helper('sales')->__('Amazon BB – ASIS-Bangalore'), 
            'Amazon BB – QC-Bangalore'      => Mage::helper('sales')->__('Amazon BB – QC-Bangalore'), 
            'FlipKart Prexo – ASIS- Bhiwandi'  => Mage::helper('sales')->__('FlipKart Prexo – ASIS- Bhiwandi'), 
            'FlipKart Prexo – QC-Bhiwandi'     => Mage::helper('sales')->__('FlipKart Prexo – QC-Bhiwandi'), 
            'FlipKart Prexo – QC-Bangalore'    => Mage::helper('sales')->__('FlipKart Prexo – QC-Bangalore'), 
            'FlipKart Prexo – ASIS-Bangalore'  => Mage::helper('sales')->__('FlipKart Prexo – ASIS-Bangalore') 
        );

        /*return array('',
            'Bhiwandi Warehouse'            => Mage::helper('sales')->__('Bhiwandi Warehouse'),
            'Tamilnadu Warehouse'           => Mage::helper('sales')->__('Tamilnadu Warehouse'),
            'Bhiwandi EB-Open Units'        => Mage::helper('sales')->__('Bhiwandi EB-Open Units'), 
            'Bhiwandi EB-Warranty stock'    => Mage::helper('sales')->__('Bhiwandi EB-Warranty stock'), 
            'Bhiwandi EB-Refurbished'       => Mage::helper('sales')->__('Bhiwandi EB-Refurbished'),            
            'Amazon BB – ASIS-Bhiwandi'     => Mage::helper('sales')->__('Amazon BB – ASIS-Bhiwandi'), 
            'Amazon BB – QC-Bhiwandi'       => Mage::helper('sales')->__('Amazon BB – QC-Bhiwandi'),
            'FlipKart Prexo – ASIS- Bhiwandi'  => Mage::helper('sales')->__('FlipKart Prexo – ASIS- Bhiwandi'), 
            'FlipKart Prexo – QC-Bhiwandi'     => Mage::helper('sales')->__('FlipKart Prexo – QC-Bhiwandi')
            );*/
        return array('',
            'Nerul Warehouse'            => Mage::helper('sales')->__('Nerul Warehouse')
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
        /*$orderId = (Mage::app()->getRequest()->getParam('order_id', '') ? Mage::app()->getRequest()->getParam('order_id', '') : 0);            
        
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
        }*/


        /***** Code change by Sonali Kosrabe as multiple invoice and shipment has been generated as per product availability in warehouse *************/

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
                $shipment = Mage::getModel('sales/order_shipment_track')->load($shipId,'parent_id');
                $orderId = $shipment->getOrderId();
                $invoiceId = $shipment->getInvoiceId();    
            }
        }


        
        $stockLocationModel = Mage::getModel('stocklocation/location')->getCollection()->addFieldToSelect('*')->addFieldToFilter('order_id',$orderId);
        /*if(in_array(null,$stockLocationModel->getColumnValues('invoice_id'))){
            $stockLocationModel = $stockLocationModel->getData();
             return $stockLocationModel[0]['stock_location'];  
        }elseif($invoiceId == 0){
            $stockLocationModel = $stockLocationModel->getData();
             return $stockLocationModel[0]['stock_location'];  
        }else{*/
            foreach ($stockLocationModel as $stocklocation) {
                $warehouse[$stocklocation->getInvoiceId()] = $stocklocation->getStockLocation();
            }
           
            if(Mage::app()->getRequest()->getParam('invoice_id')){
                return $warehouse[Mage::app()->getRequest()->getParam('invoice_id')];
            }
            if(Mage::app()->getRequest()->getParam('shipment_id')){
                return $warehouse[$invoiceId];
            }
            if(Mage::app()->getRequest()->getParam('order_id')){
                return implode('<br>',$warehouse);
            }
        //}
    }

    function getStockLocation($orderId,$invoice_id){
        $stockLocations = Mage::getModel('stocklocation/location')->getCollection()->addFieldToFilter('order_id',$orderId);
       
        foreach ($stockLocations as $stocklocation) {
            $warehouse[$stocklocation->getInvoiceId()] = $stocklocation->getStockLocation();
        }

        return $warehouse[$invoice_id];
    }

    function getWarehouseMailToSent($warehouse){
        $stockLocationModel['stock_location'] = $warehouse;
       $emails = array();
        if($stockLocationModel['stock_location']=='Bhiwandi Warehouse'){
            //$emails = array('sonali.kosrabe@wwindia.com','sonalik2788@gmail.com');
            $emails = array('kkoctransport.bhiwandi@dhl.com','Yogesh.Lokhande@dhl.com','mangesh.gandhi@dhl.com','Manish.Jadhav@dhl.com','yogesh.a@electronicsbazaar.com','jitendra.rane@dhl.com');
             
            
        }elseif($stockLocationModel['stock_location']=='Tamilnadu Warehouse'){
            //$emails = array('sonalik2788@gmail.com,zeenat.s@electronicsbazaar.com');
            $emails = array('Daniel.A@dhl.com','ajay.prdeep@dhl.com','Transport.KKOC@dhl.com','jeelan.m@amiableindia.com');

        }

        return $emails;
    }
}
	 