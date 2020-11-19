<?php
class Neo_Career_Block_Career extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCareer()     
     { 
        if (!$this->hasData('career')) {
            $this->setData('career', Mage::registry('career'));
        }
        return $this->getData('career');
        
    }

    public function getJobs()
    {
        $jobs = Mage::getModel('career/career')->getCollection();
        return $jobs;
    }
}