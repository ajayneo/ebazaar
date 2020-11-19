<?php
class Neo_Innoviti_Model_Totals_Quote extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	protected $_code = 'emi_interest';
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		parent::collect($invoice);
		
		$payment = $invoice->getOrder()->getPayment();
		if($payment->getData('method') != 'innoviti') { 
			return $this;
		}
		$baseInterest = $payment->getAdditionalInformation('interest');
		$interest = Mage::app()->getStore()->convertPrice($baseInterest);
		$invoice->setEmiInterest($baseInterest);
		$basetotal = $invoice->getBaseGrandTotal();
		$total = $invoice->getGrandTotal();
		$bgt = $invoice->getBaseGrandTotal();
		$interest_amt = (($bgt * $baseInterest) / 100);
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $interest_amt);
		$invoice->setGrandTotal($invoice->getGrandTotal() + $interest_amt);
		return $this;
	}
}
?>
