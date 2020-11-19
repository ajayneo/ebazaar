<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Sugarcode_Innoviti
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * innoviti Payment Model
 *
 * @category   Mage
 * @package    Sugarcode_Innoviti
 * @name       Sugarcode_Innoviti_Model_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Neo_Innoviti_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'innoviti';
    protected $_formBlockType = 'innoviti/form';
    //protected $_infoBlockType = 'innoviti/info';
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_emiData = array();
    protected $_order = null;
    public $_bankCode = 0;
    public $_tanureCode = 0;

    /**
     * Get Config model
     *
     * @return object Sugarcode_Innoviti_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('innoviti/config');
    }

    /**
     * Payment validation
     *
     * @param   none
     * @return  Sugarcode_Innoviti_Model_Payment
     */
    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        return $this;
    }
    
    public function assignData($data)
    {
		$result = parent::assignData($data);
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        
        $this->_bankCode = $data->getBankcode();
        $this->_tenurecode = $data->getTenurecode();
        
        $info = $this->getInfoInstance();
		$info->setAdditionalInformation('bankcode', $this->_bankCode);
		$info->setAdditionalInformation('tanurecode', $this->_tenurecode);
		$info->setAdditionalInformation('interest', $this->getInterestRate($this->_bankCode, $this->_tenurecode));
        Mage::getSingleton('core/session')->setBankCode($this->_bankCode);
        Mage::getSingleton('core/session')->setTenurecode($this->_tenurecode);
        return $this;
    }
    

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture (Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
    *  Returns Target URL
    *
    *  @return string Target URL
    */
	 
    public function getinnovitiUrl()
    {	
	
		//$url =  Mage::getStoreConfig('payment/innoviti_payment/paymenturl');	
		//$url = 'https://192.168.0.53:8443/uniPAYNet1/saleTrans';
		//$url = 'https://122.166.12.85:13001/uniPayNetGNG/saleTrans';
		$url = ' https://unipaynet.innoviti.com/uniPayNetEbazaar/saleTrans';	
		return $url;
    }

    /**
     *  Return URL for innoviti success response
     *
     *  @return	  string URL
     */
    protected function getSuccessURL ()
    {
        return Mage::getUrl('innoviti/payment/success', array('_secure' => true));
    }

    /**
     *  Return URL for innoviti notification
     *
     *  @return	  string Notification URL
     */
    protected function getNotificationURL ()
    {
        return Mage::getUrl('innoviti/payment/notify', array('_secure' => true));
    }

    /**
     *  Return URL for innoviti failure response
     *
     *  @return	  string URL
     */
    protected function getFailureURL ()
    {
        return Mage::getUrl('checkout/onepage/failure', array('_secure' => true));
    }
	
	/**
	* Return Url for Innoviti Bank notification - vithya
	* @ return string Notification URL
	*/
	
	public function getsURL ()
    {
        return Mage::getUrl('innoviti/payment/return', array('_secure' => true));
    }
	
	
    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('innoviti/form_payment', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
		$params = array('bankCode' => $this->_bankCode, 'tanureCode' => $this->_tanureCode);
        return Mage::getUrl('innoviti/payment/redirect', $params);
    }

    /**
     *  Return Payment Checkout Form Fields for request to innoviti
     *
     *  @return	  array Array of hidden form fields
     */
    public function getPaymentCheckoutFormFields ()
    {
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }

        $billingAddress = $order->getBillingAddress();

        $streets = $billingAddress->getStreet();
        $street = isset($streets[0]) && $streets[0] != ''
                  ? $streets[0]
                  : (isset($streets[1]) && $streets[1] != '' ? $streets[1] : '');

        if ($this->getConfig()->getDescription()) {
            $transDescription = $this->getConfig()->getDescription();
        } else {
            $transDescription = Mage::helper('innoviti')->__('Order #%s', $order->getRealOrderId());
        }

        if ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } elseif ($billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        } else {
            $email = '';
        }

        $fields = array(
						'account_id'       => $this->getConfig()->getAccountId(),
                       	'return'           => Mage::getUrl('innoviti/payment/success',array('_secure' => true)),
                        'product_name'     => $transDescription,
                        'product_price'    => $order->getBaseGrandTotal(),
                        'language'         => $this->getConfig()->getLanguage(),
                        'f_name'           => $billingAddress->getFirstname(),
                        's_name'           => $billingAddress->getLastname(),
                        'street'           => $street,
                        'city'             => $billingAddress->getCity(),
                        'state'            => $billingAddress->getRegionModel()->getCode(),
                        'zip'              => $billingAddress->getPostcode(),
                        'country'          => $billingAddress->getCountryModel()->getIso3Code(),
                        'phone'            => $billingAddress->getTelephone(),
                        'email'            => $email,
                        'cb_url'           => $this->getNotificationURL(),
                        'cb_type'          => 'P', // POST method used (G - GET method)
                        'decline_url'      => $this->getFailureURL(),
                      	'cs1'              => $order->getRealOrderId());

        if ($this->getConfig()->getDebug()) {
            $debug = Mage::getModel('innoviti/api_debug')
                ->setRequestBody($this->getinnovitiUrl()."\n".print_r($fields,1))
                ->save();
            $fields['cs2'] = $debug->getId();
        }

        return $fields;
    }

    public function emiCalculator()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://unipaynet.innoviti.com/uniPayNetEbazaar/emiCalculator');
		if ($ch === false)
		{
			throw new Exception(' cURL init failed');
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'merchantId=652564864653642&subMerchantId=eb12');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$txResult = curl_exec($ch);
		curl_close($ch);
		$xml = simplexml_load_string($txResult);
		$_emiData = array();
		//$total_amount = Mage::helper('checkout/cart')->getQuote()->getGrandTotal(); // original

        // start of code for app changes made by pradeep sanku
        $total_amount = Mage::helper('checkout/cart')->getQuote()->getGrandTotal();
        if(!($total_amount) && isset($_REQUEST['quote_id'])){
            $quote_model = Mage::getModel('sales/quote')->load($_REQUEST['quote_id']);
            $total_amount = $quote_model->getGrandTotal();
        }
        // end of code for app changes made by pradeep sanku

		foreach ($xml as $bank) {
			$temp = $tenure_ary = array(); 
			$name = (array)$bank->name;
			$bankCode = (array)$bank->bankCode;
			$temp['bankCode'] = $bankCode[0];
			$temp['name'] = $name[0];
			foreach($bank->tenure as $tenure) {
				$interestRate = (array)$tenure->isCs->interestRate;
				$processingFee = (array)$tenure->isCs->processingFee;
				if((float)$interestRate[0] == 0) {
					continue;
				}
				$type = (array)$tenure->type;
				$tanureCode = (array)$tenure->tenureCode;
				$tenure_ary[(int)$tanureCode[0]] = $this->emiCalc((float)$interestRate[0], (int)$tanureCode[0], (float)$total_amount, (float)$processingFee[0]);
			}
			if(!empty($tenure_ary)) {
				$temp['tenure'] = $tenure_ary;
				$_emiData[$temp['bankCode']] = $temp;
			}
		}
		$this->_emiData = $_emiData;
		return $_emiData;
	}

	public function getBankEmi($bankCode)
	{
		$emiData = $this->getEmiData();
		return $emiData[$bankCode]['tenure'];
	}
	
	public function getEmiData()
	{
		if(!empty($this->_emiData) && isset($this->_emiData)) {
			return $this->_emiData;
		}
		return $this->emiCalculator();
	}
	
	public function getBanks()
	{
		$emiData = $this->getEmiData();
		$bankslist = array();
		foreach($emiData as $emi) {
			$bankslist[$emi['bankCode']] = $emi['name'];
		}
		return $bankslist;
	}
	
	function emiCalc($annual_interest_Rate, $duration, $txn_Amt, $processingFee)
	{
		$tenure = $duration * 3;
		$part1=$txn_Amt * $annual_interest_Rate/(12*100);
		$part2=pow(1+$annual_interest_Rate/(12*100),$tenure);
		$part3=(pow(1+$annual_interest_Rate/(12*100),$tenure)) - 1;
		$emi=($part1 * $part2) / $part3;
		$interest_amount = $emi*$tenure-$txn_Amt;
		$total_Amount_Payable = $emi*$tenure;
		
		$ary['interest_amount'] = (int)ceil($interest_amount);
		$ary['including_inr'] = (int)ceil($total_Amount_Payable);
		$ary['interest'] = number_format($annual_interest_Rate,2,".","");
		$ary['duration'] = $tenure.' Months';
		$ary['processingFee'] = number_format($processingFee,2,".","");
		$ary['transaction_amount'] = $txn_Amt;
		$ary['installment'] = (int)ceil($total_Amount_Payable / $tenure);
		/*
		$interest_amount = ($amount * $interest) / 100;
		$ary['interest_amount'] = (int)ceil($interest_amount);
		$ary['including_inr'] = (int)ceil($amount + $interest_amount);
		$ary['interest'] = number_format($interest,2,".","");
		$ary['duration'] = $duration.' Months';
		$ary['processingFee'] = number_format($processingFee,2,".","");
		$ary['installment'] = (int)ceil($ary['including_inr'] / $duration);*/
		return $ary;
	}
	
	function getInterestRate($bankCode,$tanureCode)
	{
		$emiData = $this->getEmiData();
		return $emiData[$bankCode]['tenure'][$tanureCode]['interest'];
	}

}
