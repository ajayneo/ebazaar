<?php
class Neo_Gadget_Block_Trade extends Mage_Core_Block_Template{ 
	public function __construct()
    {
        parent::__construct();
        $collection = Mage::getModel('gadget/request')->getCollection();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $collection->addFieldToFilter('email',$customer->getEmail());
        $collection->addFieldToFilter('price',array('gt'=>0));
        $this->setCollection($collection);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
    
    public function getToolbarHtml()
    {
        return $this->getChildHtml('pager');
    }
}

?>