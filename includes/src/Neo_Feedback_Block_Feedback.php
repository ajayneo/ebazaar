<?php
class Neo_Feedback_Block_Feedback extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFeedback()     
     { 
        if (!$this->hasData('feedback')) {
            $this->setData('feedback', Mage::registry('feedback'));
        }
        return $this->getData('feedback');
        
    }
}