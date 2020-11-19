<?php
class Neo_Shippinge_Block_Adminhtml_Shipment extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create
{
    public function getFedexAvailableService()
    { 
		$services = array();
		$methods = Mage::getModel('shippinge/fedex')->init(); 
		$current_shipment = $this->getShipment();
		$order = $current_shipment->getOrder();
		
		foreach($methods as $method) {
			if($method['service'] == 'PRIORITY_OVERNIGHT') { continue; }
			$services[$method['service']] = $method['service'];
		} /*
        $services1 = array(
            "Fedex_SO"    =>  "Fedex - Standard Overnight",
            "Fedex_XS"    =>  "Fedex - Economy",
            "Fedex_PO"    =>  "Fedex - Priority overnight Night"
        );*/
		
        return $services;
    }

    public function getBlueAvailableService()
    {
        $services = array(
            "bluedart"  => "Bluedart"
        );
		return $services;
    }
    
    public function getInvoices()
    {
		$current_shipment = $this->getShipment();
		$order = $current_shipment->getOrder();
		$invoice_ids = $order->getInvoiceCollection()->getAllIds();
		$shipped_invoice_ids = array();
		$tracks = $order->getTracksCollection()->load();
		foreach($tracks as $track) {
			$invoice_id = $track->getInvoiceId();
			if($invoice_id != 0) {
				$shipped_invoice_ids[] = $invoice_id;
			}
		}
		$nonshipped_invoice_ids = array_diff($invoice_ids, $shipped_invoice_ids);
		$data = array();
		foreach($nonshipped_invoice_ids as $invoice_id) {
			$increment_id = Mage::getModel('sales/order_invoice')->load($invoice_id)->getIncrementId();
			$data[$invoice_id] = $increment_id;
		}
		return $data;
	}
	
	public function getEcomAvailableService()
	{
		 $services = array(
            "ecom_express"  => "Ecom Express"
        );
		return $services;
	}
}
