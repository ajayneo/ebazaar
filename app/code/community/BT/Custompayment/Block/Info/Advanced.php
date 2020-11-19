<?php
class BT_Custompayment_Block_Info_Advanced extends Mage_Payment_Block_Info
{
	protected function _prepareSpecificInformation($transport = null)
	{
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$info = $this->getInfo();
		$transport = new Varien_Object();
		$transport = parent::_prepareSpecificInformation($transport);
		$transport->addData(array(
			Mage::helper('custompayment')->__('Utr Number / Cheque Number') => $info->getApUtrNo(),
			//Mage::helper('custompayment')->__('Cheque Number') => $info->getApCheckNo(),
			Mage::helper('custompayment')->__('Bank Name') => $info->getApBankName()
		));
		return $transport;
	}
}  