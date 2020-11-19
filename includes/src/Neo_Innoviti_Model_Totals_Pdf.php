<?php
class Neo_Innoviti_Model_Total_Pdf extends Mage_Sales_Model_Order_Pdf_Total_Default
{
	public function getTotalsForDisplay()
	{
		$order = $this->getOrder();
		$payment = $order->getPayment();
		if($payment->getMethod('method') == 'innoviti') {
			$fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
			$grand_total = (float)$order->getSubtotal();
			$interest = $payment->getAdditionalInformation('interest');
			$tenurecode = $payment->getAdditionalInformation('tenurecode');
			$details = Mage::helper('innoviti')->emiCalc($interest, $tenurecode, $grand_total);
			$interest_amount = $details['interest_amount'];

			$totals = array(array(
				'label' => 'Emi Interest',
				'amount' => $interest_amount,
				'font_size' => $fontSize,
			));
			return $totals;
		}
	}
}
?>
