<?php
class Neo_Pdf_Block_Order_Info extends Mage_Sales_Block_Order_Info
{
   public function getLinks()
    {
        $this->checkLinks();
        return $this->_links;
    }

    public function checkLinks()
    {
        $order = $this->getOrder();
        if (!$order->hasInvoices()) {
            unset($this->_links['invoice']);
        }
		if (!$order->hasReceipts()) {
            unset($this->_links['receipt']);
        }
        if (!$order->hasShipments()) {
            unset($this->_links['shipment']);
        }
        if (!$order->hasCreditmemos()) {
            unset($this->_links['creditmemo']);
        }
    }
	
}
