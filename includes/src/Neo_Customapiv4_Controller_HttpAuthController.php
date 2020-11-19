<?php
class Neo_Customapiv4_Controller_HttpAuthController extends Mage_Core_Controller_Front_Action
{
	public function authenticate()
	{
		header('WWW-Authenticate: Basic realm="Test Authentication System"');
		header('HTTP/1.0 401 Unauthorized');
		echo "You must enter a valid login ID and password to access this resource\n";
		exit;
	}

	public function preDispatch()
	{
		$username = Mage::getStoreConfig('neo_api_section/neo_api_group/http_auth_username');
		$password = Mage::getStoreConfig('neo_api_section/neo_api_group/http_auth_password');
		$username = 'admin';
		$password = 'admin123';
		//$password = Mage::helper('core')->decrypt($password);

		if(!isset($_SERVER['PHP_AUTH_USER'])) {
			$this->authenticate();
		} else if($_SERVER['PHP_AUTH_USER']!=$username || $_SERVER['PHP_AUTH_PW']!=$password) {
			header('WWW-Authenticate: Basic realm="Test Authentication System"');
			header('HTTP/1.0 401 Unauthorized');
			echo "You must enter a valid login ID and password to access this resource\n";
			exit;
		}
		$_SERVER['PHP_AUTH_USER'] = '';
		$_SERVER['PHP_AUTH_PW'] = '';

		/* verify the token and check the user access type
		$api = $this->authentication($_POST['token']);*/
	}

	public function authentication($token)
	{
		$app_token = md5(Mage::getStoreConfig('neo_api_section/neo_api_group/api_token'));

		try{
			if($this->checkExist($_SERVER['HTTP_USER_AGENT']) || !isset($_SERVER['HTTP_USER_AGENT']))
			{
				if($app_token === $token){}
				else{
					echo "ERROR: Invalid token provided, please check the token you have passed.";exit;
				}
			}else{
				echo "ERROR : Invalid action found, service can not be called from browser."; exit;
			}
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";exit;
		}
	}

	public function checkExist($value_to_check)
	{
//echo stripos($value_to_check,"Android");
		$status = false;
		$val_arr = array("iPod","iPhone","iPad","Android","webOS","Appcelerator Titanium");
		for($i = 0; $i < count($val_arr); $i++) {
			 if(stripos($value_to_check,$val_arr[$i]) !== false) {
					$status = true;
			}
		}
		return $status;
	}

	public function validateCustomer($customer_id)
	{
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		$customer_id = $customer->getId();
		if($customer_id < 1) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid customer id'));
			exit;
		}
	}

	public function validateProduct($product_id)
	{
		$product = Mage::getModel('catalog/product')->load($product_id);
		$product_id = $product->getId();
		if($product < 1) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid product id'));
			exit;
		}
	}

	public function validateAddress($address_id)
	{
		$address = Mage::getModel('customer/address')->load($address_id);
		$addressId = $address->getId();
		if($addressId != $address_id) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid address id'));
			exit;
		}
	}

	public function validateOrder($order_id)
	{
		$order = Mage::getModel('sales/order')->load($order_id);
		$orderId = $order->getId();
		$customer_model = Mage::getModel('customapiv4/customer');
		if($order_id != $orderId) {
			echo json_encode(array('status' => 0, 'message' => 'Please provide valid order id'));
			exit;
		}
	}
}
?>
