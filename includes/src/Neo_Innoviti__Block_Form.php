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
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://122.166.12.85:13001/uniPayNetGNG/emiCalculator');
		if ($ch === false)
		{
			throw new Exception(' cURL init failed');
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'merchantId=656463565655623&subMerchantId=bg67');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$txResult = curl_exec($ch);
		curl_close($ch);
		$xml = simplexml_load_string($txResult);
		$_emiData = array();
		$total_amount = $this->helper('checkout/cart')->getQuote()->getGrandTotal(); 
		foreach ($xml as $bank) {
			$temp = $tenure_ary = array();
			$name = (array)$bank->name;
			$bankCode = (array)$bank->bankCode;
			$temp['bankCode'] = $bankCode[0];
			$temp['name'] = $name[0];
			foreach($bank->tenure as $tenure) {
				$interestRate = (array)$tenure->isCs->interestRate;
				if((float)$interestRate[0] == 0) {
					continue;
				}
				$type = (array)$tenure->type;
				$tanureCode = (array)$tenure->tenureCode;
				$tenure_ary[(int)$tanureCode[0]] = $this->emiCalc((float)$interestRate[0], (int)$tanureCode[0], (float)$total_amount);
			}
			if(!empty($tenure_ary)) {
				$temp['tenure'] = $tenure_ary;
				$_emiData[$temp['bankCode']] = $temp;
			}
		}
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
		$including_inr = $amount + (($amount * $interest) / 100);
		$installment = $ary['including_inr'] / $duration;
		$ary['including_inr'] = number_format($including_inr,2,'.','');
		$ary['interest'] = number_format($interest,2,'.','');
		$ary['duration'] = $duration.' Months';
		$ary['installment'] = number_format($installment,2,'.','');
		return $ary;
	}
}
?>
