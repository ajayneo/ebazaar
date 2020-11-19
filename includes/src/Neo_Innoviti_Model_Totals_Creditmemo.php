<?php
class Neo_Innoviti_Model_Totals_Quote extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	protected $_code = 'emi_interest';
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		parent::collect($creditmemo);
		
		$payment = $creditmemo->getQuote()->getPayment();
		if($payment->getData('method') != 'innoviti') { 
			return $this;
		}
		$baseInterest = $payment->getAdditionalInformation('interest');
		$interest = Mage::app()->getStore()->convertPrice($baseInterest);
		$creditmemo->setEmiInterest($baseInterest);
		$basetotal = $creditmemo->getBaseGrandTotal();
		$total = $creditmemo->getGrandTotal();
		$bgt = $creditmemo->getBaseGrandTotal();
		$details = Mage::helper('innoviti')->emiCalc($baseInterest,$tenurecode, $address->getBaseGrandTotal());
		$interest_amt = $details['interest_amount'];
		$address->setEmiInterest($interest_amt);
		$address->setBaseGrandTotal($details['including_inr']);
		$address->setGrandTotal($details['including_inr']);
		return $this;
	}
}
?>
