<?php
    class Neo_SMS_Model_Observer {
        public function confirmation_of_order($observer){
            $order = $observer->getEvent()->getOrder();
            $order_inc_id = $order->getIncrementId();
            $state= $order->getStatus();
            $customer_id = $order->getCustomerId();
            $customerData = Mage::getModel('customer/customer')->load($customer_id);
            $customer_mobile = $customerData->getMobile();
            /*if(empty($customer_mobile)){
                $customer_mobile = $customerData->getTelephone();
            }*/
            //$payment_method = $order->getPayment()->getMethodInstance()->getCode();

            Mage::log($customer_mobile,null,'customer.log');

            if($state == "processing"){
                $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
                $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
                $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

                $to=$customer_mobile;
                $msg='Your Elecronics bazaar order '.$order_inc_id.' has been confirmed and being processed.Do check your email for additional details. Thank You!';
                Mage::log('Confirmation of Order '. $order_inc_id .' Sent to '.$customerData->getName().' on '.$to,null,'neo_sms_confirmation_of_order.log');
                $msg=urlencode($msg);
                $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

                $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result=curl_exec($ch);
                curl_close($ch);
            }
        }

        public function confirmation_of_shipment($observer){
            $order = $observer->getEvent()->getOrder();
            $order_inc_id = $order->getIncrementId();
            $state= $order->getStatus();
            $customer_id = $order->getCustomerId();
            $subtotal_incl_tax = $order->getSubtotalInclTax();
            $customerData = Mage::getModel('customer/customer')->load($customer_id);
            $customer_mobile = $customerData->getMobile();
            $payment_method = $order->getPayment()->getMethodInstance()->getCode();

			$currentdate = Mage::getModel('core/date')->date('Y-m-d');
			$date=date_create($currentdate);
    		$current_formated_date = date_format($date,"d/m/Y");

			/*$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($order)->load();

        	foreach ($shipmentCollection as $shipment){
            	foreach($shipment->getAllTracks() as $tracknum)
            	{
                	$tracknums[]=$tracknum->getNumber();
					$tracknums[]=$tracknum->getTitle();
            	}
        	}*/

			//Mage::log('a',null,'P.log');
            $shipment_created;
            foreach($order->getShipmentsCollection() as $shipment){
                global $shipment_created;
                $shipment_created = $shipment->getCreatedAt();
            }
            $date=date_create($shipment_created);
            $shipment_created_formated = date_format($date,"d/m/Y");

            if($state == "complete"){
                $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
                $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
                $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

                $to=$customer_mobile;
                $excpected_delivery_date = 'With in Seven Days';

				$msg='Your Electronics Bazaar Order '.$order_inc_id.' is shipped by '.$tracknums[1].'with tracking number '.$tracknums[1].'on '.$current_formated_date.'and expected delivery date is '.$excpected_delivery_date.'. Thank You! ';

                Mage::log('Confirmation of Order '. $order_inc_id .' Shipment Sent to '.$customerData->getName().' on '.$to,null,'neo_sms_confirmation_of_shipment.log');
                $msg=urlencode($msg);
                $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

                $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result=curl_exec($ch);
                curl_close($ch);
            }
        }

        public function order_success($observer){
            $orderIds = $observer->getData('order_ids');
            foreach($orderIds as $_orderId){
                $order = Mage::getModel('sales/order')->load($_orderId);
                $order_inc_id = $order->getIncrementId();
                $customer_id = $order->getCustomerId();
                if(!empty($customer_id)){
                    $customerData = Mage::getModel('customer/customer')->load($customer_id);
                    $customer_mobile = $customerData->getMobile();
                    Mage::log($customer_mobile,null,'dpk.log');
                    if(empty($customer_mobile)){
                        $customer_mobile = $order->getBillingAddress()->getFax();
                        $customerData->setMobile($customer_mobile);
                        $customerData->save();
                    }
                    $customer_name = $customerData->getName();
                }else{
                    $customer_mobile = $order->getBillingAddress()->getFax();
                    $customer_name = $order->getBillingAddress()->getName();
                }
                //$customerData = Mage::getModel('customer/customer')->load($customer_id);
                //$customer_mobile = $customerData->getMobile();
                $payment_method = $order->getPayment()->getMethodInstance()->getCode();
                $payment_method_title = $order->getPayment()->getMethodInstance()->getTitle();

				$currentdate = Mage::getModel('core/date')->date('Y-m-d H:i:s');
				$date=date_create($currentdate);
    			$current_formated_date = date_format($date,"d/m/Y H:i:s");

                $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
                $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
                $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

                $to=$customer_mobile;

                //$msg='Your Electronics Bazaar order '.$order_inc_id.' paid with '.$payment_method_title.' is successfully placed. Thank You!';
                $msg='Dear '.$customer_name.',your Electronics Bazaar Order '.$order_inc_id.' has been confirmed and being processed. Check your email for additional details. Thank You! '.$current_formated_date.'.';
                //Mage::log('Confirmation of Order '. $order_inc_id .' Success Sent to '.$customerData->getName().' on '.$to,null,'neo_sms_order_success.log');
                $msg=urlencode($msg);
                $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

                $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result=curl_exec($ch);
                curl_close($ch);
            }
        }

        public function send_dob_wish_sms($observer){
            $currentdate = Mage::getModel('core/date')->date('Y-m-d');
			//Mage::log("Birthday Wishes Sent to ",null,'neo.log');
            $date=date_create($currentdate);
            $current_formated_date = date_format($date,"d/m/Y");
            $customers = Mage::getResourceModel('customer/customer_collection')->joinAttribute('dob','customer/dob', 'entity_id');
            $customers = $customers->addAttributeToFilter('dob',array('eq',$currentdate));
            //Mage::log("Birthday Wishes Sent to ",null,'neo.log');

			$customer_mobile = array();
            foreach($customers as $customer){
                $cus_dob = $customer->getDob();
                $dob_date=date_create($cus_dob);
                $cus_dob_formated_date = date_format($dob_date,"d/m/Y");
                if($cus_dob_formated_date == $current_formated_date){
                    $customer_id = $customer->getId();
                    $customerData = Mage::getModel('customer/customer')->load($customer_id);
                    $customer_mobile[] = $customerData->getMobile();
                }
            }

			Mage::log($customer_mobile,null,'neo_sms_send_dob_wish_sms.log');
			$mobile_customer = implode(',',$customer_mobile);
			$uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
            $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
            $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

			$to=$mobile_customer;
			Mage::log("Birthday Wishes Sent to ".$to,null,'neo_sms_send_dob_wish_sms.log');
			$msg='Electronics Bazaar Wishes You Happy Birthday';

			$msg=urlencode($msg);
			$data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

			$ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result=curl_exec($ch);
			curl_close($ch);
            //$cus = $cus->addAttributeToFilter('dob',array('eq',$currentdate));
	}

    }

?>