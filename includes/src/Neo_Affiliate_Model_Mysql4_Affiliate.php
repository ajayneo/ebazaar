<?php
	class Neo_Affiliate_Model_Mysql4_Affiliate extends Mage_Core_Model_Mysql4_Abstract
	{
		public function _construct()
	    {   
	        $this->_init('neoaffiliate/affiliate', 'affiliate_id');
	    }
	}
?>