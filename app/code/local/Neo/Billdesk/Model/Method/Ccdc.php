<?php
class Neo_Billdesk_Model_Method_Ccdc extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'billdesk_cc_dc';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('billdesk/payment/ccdcredirect', array('_secure' => true));
	}
}
?>