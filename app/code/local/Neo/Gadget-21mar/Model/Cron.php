<?php
class Neo_Gadget_Model_Cron{	
	public function test(){
		//do something
		Mage::log("Cron Works!!",null,'cron.log',true);
	}

	/**
	**@Dec Daily update gadget status
	**@Auth Mahesh Gurav
	**@Date 07 Feb 18
	**/
	public function statusupdate(){
		$gadgets = Mage::getModel('gadget/request')->getCollection();
        $gadgets->addFieldToSelect('awb_number');
        $gadgets->addFieldToFilter('awb_number',array('neq'=>NULL));
        $gadgets->addFieldToFilter('status',array('neq'=>'delivered'));
        $gadgets->getSelect()->limit(100);

        $awb_number = array();
        foreach($gadgets as $gadget){
            if($gadget->getAwbNumber()){
                $awb_number[] = $gadget->getAwbNumber();
            }
        }

        $queryArray['awb_number'] = implode(",", $awb_number);
        $queryArray['order_id'] = '';
        
        $tracking_status = Mage::getModel('shippinge/ecom')->multiShipmentTracking($queryArray);
        if(count($tracking_status) > 0){
            foreach ($tracking_status as $id => $status) {
                $gadget_model = Mage::getModel('gadget/request')->load($id);
                if($status !== $gadget_model->getStatus()){
                    $gadget_model->setStatus($status);
                    $gadget_model->save();
                    Mage::log("Gadget #$id => $status",null,'cron.log',true);
                }
            }
        }
	}
}