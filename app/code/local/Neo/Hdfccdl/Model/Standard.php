<?php
class Neo_Hdfccdl_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'hdfc_cdl';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('hdfccdl/payment/redirect', array('_secure' => true));
	}
}
?>