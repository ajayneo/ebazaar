<?php
class Neo_Replace_Model_Replace extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'replace';
	protected $_formBlockType = 'replace/form_replace';
	protected $_infoBlockType = 'replace/info_replace';
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = true;	

	public function assignData($data)
	{
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		$info = $this->getInfoInstance();
		$info->setOrderNo($data->getOrderNo());
		return $this;
	}

	public function in_array_r($item , $array){
	    return preg_match('/"'.$item.'"/i' , json_encode($array));
	}

	public function validate()
	{
		parent::validate();

		$info = $this->getInfoInstance();

		$no = $info->getOrderNo();

		$customerData = Mage::getSingleton('customer/session')->getCustomer();
		$orderCollection = Mage::getResourceModel('sales/order_collection')
        ->addFieldToSelect('increment_id') 
        ->addFieldToFilter('customer_id', $customerData->getId())->getData();

        if($no != '')
        {
			if($this->in_array_r(trim($no),$orderCollection)){
				return $this;
			}else{
				$errorCode = 'invalid_data';
				$errorMsg = $this->_getHelper()->__('Order No provided does not exist or doesnt belong to you.');
				Mage::throwException($errorMsg);
			}

		return $this;
	   }
	}
}
?>
