<?php

class BT_Custompayment_Model_Custompayment extends Mage_Payment_Model_Method_Abstract
{
     
    protected $_code = 'custompayment';	
	protected $_formBlockType = 'custompayment/form_advanced';
	protected $_infoBlockType = 'custompayment/info_advanced';    

	public function assignData($data)
	{
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		$info = $this->getInfoInstance();
		$info->setApUtrNo($data->getApUtrNo());
		$info->setApCheckNo($data->getApCheckNo());
		$info->setApBankName($data->getApBankName());
		return $this;
	}
}
