<?php
	class Neo_Customapiv4_StaticController extends Mage_Core_Controller_Front_Action
	{
		public function updateOrderPaymentIosAction()
		{
			Mage::log($_REQUEST,null,'billdesk-ios.log');
			$msg = $_REQUEST['msg'];

      $strpos = strrpos($msg,"|");
      $strlength = strlen(substr($msg,$strpos));
      $str_without_checksum = substr($msg, 0, -$strlength);
      $str_withonly_checksum = substr($msg,$strpos+1);
      /*$checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');

      $checksum = hash_hmac('sha256',$str_without_checksum,$checksum_key,false);
      $checksum = strtoupper($checksum);

      if($checksum == $str_withonly_checksum){
          $msg_params = explode('|',$msg);
          Mage::log($msg_params,null,'dpk-billdesk.log');
          $getChecksum = Mage::getSingleton('core/session')->getChecksum();
          $billdesk_checksum = $msg_params[25];
      }*/
      $msg_params = explode('|',$msg);
			$response_msg_bd = $msg_params[24];
			$response_msg_code = $msg_params[14];
			$msg_quoteId_userId = $msg_params[1];
      $msg_params_quoteId = explode('_',$msg_quoteId_userId);
      $quote_id = $msg_params_quoteId[1];
      $userId = $msg_params_quoteId[2];
			/*Mage::log('msg',null,'ru.log');
			Mage::log($msg,null,'ru.log');
			//$order_id = $_REQUEST['order_id'];
			Mage::log('order_id',null,'ru.log');
			Mage::log($order_id,null,'ru.log');
			$transaction_id = $_REQUEST['transaction_id'];
			$status = $_REQUEST['status'];*/
			try {
				// create order for IOS for bill desk payment method
				$createOrder = new Mage_Checkout_Model_Cart_Api();
        $order_id = $createOrder->createOrder($quote_id);
        if($order_id) {
          // for affiliates code
          /*if(isset($_REQUEST['repcode'])){
              $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
              $quote = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);

              $observer = new Varien_Event_Observer();
              $args = array('order' => $order,'quote' =>$quote);
              $observer->addData($args);
              Mage::getModel('neoaffiliate/observer')->checkoutTypeOnepageSaveOrder($observer);
          }*/
          $order_sms = Mage::getModel('sales/order')->loadByIncrementId($order_id);
          $order_id_sms = $order_sms->getId();
          Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($order_id_sms)));

          // update quote set as inactive
          $quote_update = Mage::getModel('sales/quote')->load($quote_id);
          $quote_update->setIsActive(0);
          $quote_update->save();
        }
				//$this->validateOrder($order_id);
				//$order_model = Mage::getModel('sales/order')->loadByIncrementId($order_id);
				//$payment_code = $order_model->getPayment()->getMethodInstance()->getCode();

				/*if($payment_code == 'billdesk_cc_dc'){
				}*/

				/*
				$observer = new Varien_Event_Observer();
				$args = array('order_ids' => array($order_id));
				$observer->addData($args);*/
				//Mage::getModel('sms/observer')->order_success($observer);
				echo json_encode(array('status' => 1, 'message' => $msg));
			} catch(Exception $ex) {
				echo json_encode(array('status' => 0, 'message' => $msg));
				exit;
			}
		}

		/**
	   	 * @desc : update order status after order place / on payment response
	     * @author : bhargav rupapara
	     */

		public function updateOrderPaymentAction()
		{
			Mage::log($_REQUEST,null,'billdesk.log');
			Mage::log($_REQUEST,null,'dpk.log');
			///Mage::log("hiiiii",null,'dpk.log');
			//echo $_POST["msg"];
			$msg = $_POST["msg"];
			$msg_params = explode('|',$msg);
      $msg_quoteId_userId = $msg_params[1];
      $msg_params_quoteId = explode('_',$msg_quoteId_userId);
      $quote_id = $msg_params_quoteId[1];
      $userId = $msg_params_quoteId[2];

      $createOrder = new Mage_Checkout_Model_Cart_Api();
      $order_id = $createOrder->createOrder($quote_id);
      if($order_id) {
	      // for affiliates code
	      /*if(isset($_REQUEST['repcode'])){
	          $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
	          $quote = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);

	          $observer = new Varien_Event_Observer();
	          $args = array('order' => $order,'quote' =>$quote);
	          $observer->addData($args);
	          Mage::getModel('neoaffiliate/observer')->checkoutTypeOnepageSaveOrder($observer);
	      }*/
	      $order_sms = Mage::getModel('sales/order')->loadByIncrementId($order_id);
	      $order_id_sms = $order_sms->getId();
	      Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($order_id_sms)));

	      // update quote set as inactive
	      $quote_update = Mage::getModel('sales/quote')->load($quote_id);
	      $quote_update->setIsActive(0);
	      $quote_update->save();

      }
			// REad msg, status, make db operation,

			// send as it is
			echo '<script type="text/javascript">
			        function getMsg(){
			                var msg = "'.$msg.'";
			                AndroidFunction.gotMsg(msg);
			        }
			        getMsg();
			</script>';
			die;
			/*
			$msg = $_REQUEST['msg'];

        $strpos = strrpos($msg,"|");
        $strlength = strlen(substr($msg,$strpos));
        $str_without_checksum = substr($msg, 0, -$strlength);
        $str_withonly_checksum = substr($msg,$strpos+1);
        $msg_params = explode('|',$msg);
				$response_msg_bd = $msg_params[24];
			try {
				echo json_encode(array('status' => 1, 'message' => $response_msg_bd));
			} catch(Exception $ex) {
				echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
				exit;
			}*/
		}
    /**
		 * @desc : contact us
		 * @author : bhargav rupapara
		 */

		public function contactUsHtmlAction()
		{
			$html = '<div style="min-width: 320px;">
    		<div style="margin: 0px auto; border: 1px solid #dedede; width: 95%;">

    			<ul style=" display: block; list-style: outside none none; margin: 0; padding:10px;">
    				<li style="clear: both; display: block; margin: 0 0 15px; width: 100%;overflow: hidden;">
    					<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
    						<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-call.png" alt="call">
    					</div>
    					<div style="width: 80%; float: left;">
    						<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Contact Number</label>
    						<span style="font-size: 15px; line-height: 18px; color:#313131">1800-266-4000<br>Monday - Friday, 9:30am - 6:30pm IST.</span>
    					</div>
    				</li>

    				<li style="clear: both; display: block; margin: 0 0 15px; width: 100%; overflow: hidden;">
    					<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
    						<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-email.png" alt="Email">
    					</div>
    					<div style="width: 80%; float: left;">
    						<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Email</label>
    						<span style="font-size: 15px; line-height: 18px; color:#313131"><a href="mailto:support@electronicsbazaar.com">support@electronicsbazaar.com</a></span>
    					</div>
    				</li>



    				<li style="clear: both; display: block; margin: 0px; width: 100%; overflow: hidden;">
    					<div style="display: block;float: left; margin-right: 10px; text-align: center; width: 30px;">
    						<img src="http://cdn.electronicsbazaar.com/skin/frontend/rwd/electronics/images/contact-pin.png" alt="location">
    					</div>
    					<div style="width: 80%; float: left;">
    						<label style= "color: #313131; display: block; font-size: 15px; margin: 0 0 5px; padding: 0; width: 100%; font-weight:bold;">Address</label>
    						<span style="font-size: 15px; line-height: 18px; color:#313131">ElectronicsBazaar.com <br> 415, Hubtown Solaris,N.S.Phadke Marg,Near East West Flyover Bridge,Andheri East, Mumbai,Maharastra - 400069</span>.
    					</div>
    				</li>
    			</ul>
    		</div>
    	         </div>';
      echo $html;
		}

    /**
    *@desc : provide message hash for payzapp
    */
    public function generateHashPayZappAction()
    {
      $msg_tobe_hashed = $_POST["payzappHashKey"];
      Mage::log($_POST["payzappHashKey"],null, "dpkpayzapp.log");
      $hash = base64_encode(hash('sha256', $msg_tobe_hashed, true));
      Mage::log($hash,null, "dpkpayzapp.log");
      try {
        echo json_encode(array('status' => 1, 'message' => $hash));
      } catch(Exception $ex) {
        echo json_encode(array('status' => 0, 'message' => $msg));
      }
      exit;
    }
}
?>
