<?php
class Neo_Innoviti_Block_Form extends Mage_Payment_Block_Form
{
	protected $_emiData;
	
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('innoviti/form.phtml');
	}
	
	public function emiCalculator()
	{
		$_emiData = $this->getMethod()->emiCalculator();
		$this->_emiData = $_emiData;
		return $_emiData;
	}

	public function getBankEmi($bankCode)
	{
		$emiData = $this->getEmiData();
		return $emiData[$bankCode]['tenure'];
	}
	
	public function getEmiData()
	{
		if(!empty($this->_emiData) && isset($this->_emiData)) {
			return $this->_emiData;
		}
		return $this->emiCalculator();
	}
	
	public function getBanks()
	{
		$emiData = $this->getEmiData();
		$bankslist = array();
		foreach($emiData as $emi) {
			$bankslist[$emi['bankCode']] = $emi['name'];
		}
		return $bankslist;
	}
	
	function emiCalc($interest, $duration, $amount)
	{
		$duration = $duration * 3;
		$ary['including_inr'] = $amount + (($amount * $interest) / 100);
		$ary['interest'] = $interest;
		$ary['duration'] = $duration.' Months';
		$ary['installment'] = $ary['including_inr'] / $duration;
		return $ary;
	}
}
