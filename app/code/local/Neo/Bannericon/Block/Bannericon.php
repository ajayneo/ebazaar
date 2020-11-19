<?php
class Neo_Bannericon_Block_Bannericon extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBannericon()     
     { 
        if (!$this->hasData('bannericon')) {
            $this->setData('bannericon', Mage::registry('bannericon'));
        }
        return $this->getData('bannericon');
        
    }
}