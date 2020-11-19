<?php
class Neo_Gadget_Block_Detail extends Mage_Core_Block_Template{ 
	public function __construct()
    {
        parent::__construct();
            $order_id = $this->getRequest()->getParam('order_id');
            $order = Mage::getModel('gadget/request')->load($order_id);
            $this->setOrder($order);
    }
    
    
}

?>