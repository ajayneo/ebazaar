<?php
class Neo_Innoviti_Model_Totals_Quote extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_code = 'emi_interest';
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		
		$payment = $address->getQuote()->getPayment();
		if($payment->getData('method') != 'innoviti') { 
			return $this;
		}
		$bankcode = $_REQUEST['payment']['bankcode'];
		$tenurecode = $_REQUEST['payment']['tenurecode'];
		
		$baseInterest = $payment->getMethodInstance()->getInterestRate($bankcode,$tenurecode);
		//$baseInterest = $payment->getAdditionalInformation('interest');
		$interest = Mage::app()->getStore()->convertPrice($baseInterest);
		$basetotal = $address->getBaseGrandTotal();
		$total = $address->getGrandTotal();
		$bgt = $address->getBaseGrandTotal();
		$details = Mage::helper('innoviti')->emiCalc($baseInterest,$tenurecode, $address->getBaseGrandTotal());
		$interest_amt = $details['interest_amount'];
		$address->setEmiInterest($interest_amt);
		$address->setBaseGrandTotal($details['including_inr']);
		$address->setGrandTotal($details['including_inr']);
		return $this;
	}
	
	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$emi_interest = $address->getEmiInterest();
		$payment = $address->getQuote()->getPayment();
		if($payment->getData('method') != 'innoviti') { 
			return $this;
		}
		$address->addTotal(array(
			'code' => $this->_code,
			'title' => 'Emi Interest Amount',
			'value' => $emi_interest,
		));
		return $this;
	}
}
?>
