<?php
class Neo_Custom_Block_Custom extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCustom()     
     { 
        if (!$this->hasData('custom')) {
            $this->setData('custom', Mage::registry('custom'));
        }
        return $this->getData('custom');
        
    }
}