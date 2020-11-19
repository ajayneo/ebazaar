<?php
	class Neo_Shippinge_Model_Shippinge extends Mage_Core_Model_Abstract
	{
		public function _construct()
		{
			parent::_construct();
			$this->_init('shippinge/shippinge');
		}
		
		public function savetrack($data)
		{
			$shippinge_model = Mage::getModel('shippinge/shippinge');
			$final_array_status = array('Delivered','Delivery exception');
			$order_id = $data['order_id'];
			$ship_id = $shippinge_model->unsetData()->load($order_id,'order_id');
			if($ship_id->getShippingeId()){
				if($data['track_status'] == 'Delivery exception'){
					$ship_id->setActionstatus($data['actionstatus']);
				}
					$ship_id->setTrackStatus($data['track_status'])->save();
				}else{
					$shippinge_model->unsetData()->setData($data)->save();	
				}

				if(in_array($data['track_status'], $final_array_status)){
					Mage::log($data,null,'PradeepOrder.log');
					Mage::log("Saving For ".$order_id,null,'PradeepOrder.log');
					Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					$order_model = Mage::getModel('sales/order')->load($order_id);
					$order_model->setTrackstatuscode('1');
					$order_model->save();
					Mage::log("Saved For ".$order_id,null,'PradeepOrder.log');
				}
		}
	}
?>