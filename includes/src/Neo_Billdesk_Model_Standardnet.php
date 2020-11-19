<?php
class Neo_Billdesk_Model_Standardnet extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'billdesk_netbanking';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('billdesk/payment/redirect', array('_secure' => true));
	}
}
?>