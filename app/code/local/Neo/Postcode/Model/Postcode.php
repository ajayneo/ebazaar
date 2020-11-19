<?php
class Neo_Postcode_Model_Postcode extends Mage_Core_Model_Abstract
{
	const ENTITY    = 'postcode_postcode';
    const CACHE_TAG = 'postcode_postcode';
    
    protected $_eventPrefix = 'postcode_postcode';
    protected $_eventObject = 'postcode';
    
	public function _construct()
	{
		parent::_construct();
		$this->_init('postcode/postcode');
	}
	
	public function _beforeSave()
	{
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if($this->isObjectNew()) {
			$this->setCreatedAt($now);
		}
		$this->setUpdatedAt($now);
		return $this;
	}
}
?>
