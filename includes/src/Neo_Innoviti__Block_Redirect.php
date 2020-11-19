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
 * Redirect to innoviti
 *
 * @category    Mage
 * @package     Sugarcode_Innoviti
 * @name        Sugarcode_Innoviti_Block_Payment_Redirect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Neo_Innoviti_Block_Redirect extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
		$payment = Mage::getModel('innoviti/standard');
        $form = new Varien_Data_Form();
		$order=$this->getOrder();
		$path = $payment->getinnovitiUrl();		
		//$actionURL =$payment->getinnovitiUrl();

		$form->setAction($path)
            ->setId('innoviti_payment_checkout')
            ->setName('innoviti_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
		 $billingAddress = $order->getBillingAddress();
		 
		 if ($order->getCustomerEmail()) {
            $email = $order->getCustomerEmail();
        } elseif ($billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        } else {
            $email = '';
        }
		
		$orderId=$order->getIncrementId();
//		$merchantId =Mage::getSingleton('innoviti/config')->getConfigData('merchantid'); //merchnatId
//		$submerchantId =Mage::getSingleton('innoviti/config')->getConfigData('submerchantid'); //subMerchantId

$merchantId = $merchantid = '656463565655623';
$subMerchantId = $submerchantid = 'bg67';

		$currency ='INR'; //currency
		$email =$email; // customer Email-Id
		$name = trim($billingAddress->getFirstname()); //Customer Name
		$mobile=$billingAddress->getTelephone();// customer mobile number
		$proSku='Electronics';// Product Catagory
		if($order->getParentQuoteId()!=''){ //for split order
				$parentQuoteId	= $order->getParentQuoteId();
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
				$getGTQuery = "SELECT SUM(grand_total) as order_gt FROM sales_flat_order WHERE parent_quote_id=".$parentQuoteId;
				$gTQueryRes = $connection->query($getGTQuery);
				$gTData = $gTQueryRes->fetch();
				$amt = $gTData['order_gt'];
			}else {
				$amt =$order->getGrandTotal(); // amount
		}
		//$redirUrl =Mage::getUrl('innoviti/payment/complete', array('_secure' => true)); //Merchant redirectURL
		$redirUrl =Mage::getUrl('innoviti/payment/complete'); //Merchant redirectURL
		$cur=Mage::app()->getStore()->getCurrentCurrencyCode();		
		$amt=number_format($amt, 2, '.', '');
		
		//$saltValue = Mage::getSingleton('innoviti/config')->getConfigData('saltvalue');; //salt Value
		$saltval = 'bg@#8&';
		$bankCode = Mage::getSingleton('core/session')->getBankCode();
		$tenureCode = Mage::getSingleton('core/session')->getTenurecode();
		//$processingCode = $order->getPayment()->getPoNumber(); // Processing Code
		$processingCode = $bankCode.$tenureCode.'0000';
		
		//storing the request sending to innoviti payment gateway 
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$sql = 'UPDATE sales_flat_order set payment_request_innoviti = "'.$orderId.'|'.$merchantId.'|'.$submerchantId.'|'.$amt.'|'.$currency.'|'.$proSku.'|'.$name.'|'.$mobile.'|'.$email.'|'.$redirUrl.'|'.$saltValue." | [checksum =$chksum]".'" where entity_id='.$order->getId();
		$connection->query($sql);
		
       // $form->addField('checkout', 'hidden', array('name'=>'checkout', 'value'=>$path));
       $checksum_string = $orderId."|".$merchantId."|".$subMerchantId."|".$amt."|".$cur."|".$proSku."|".$name."|".$mobile."|".$email."|".$redirUrl."|".$saltval;
       Mage::log('checksum string '.$checksum_string,null,'bhargav.log');
       $chksum =  MD5($checksum_string);
        $form->addField('amt', 'hidden', array('name'=>'amt', 'value'=>$amt));
        $form->addField('processingCode', 'hidden', array('name'=>'processingCode', 'value'=>$processingCode));
        $form->addField('merchantId', 'hidden', array('name'=>'merchantId', 'value'=>$merchantId));
        $form->addField('subMerchantId', 'hidden', array('name'=>'subMerchantId', 'value'=>$subMerchantId));
        $form->addField('orderId', 'hidden', array('name'=>'orderId', 'value'=>$orderId));
        $form->addField('redirUrl', 'hidden', array('name'=>'redirUrl', 'value'=>$redirUrl));
        $form->addField('cur', 'hidden', array('name'=>'cur', 'value'=>$cur));
        $form->addField('chksum', 'hidden', array('name'=>'chksum', 'value'=>$chksum));
        $form->addField('Cname', 'hidden', array('name'=>'Cname', 'value'=>$name));
        $form->addField('emailId', 'hidden', array('name'=>'emailId', 'value'=>$email));
        $form->addField('mobile', 'hidden', array('name'=>'mobile', 'value'=>$mobile));
        $form->addField('proSku', 'hidden', array('name'=>'proSku', 'value'=>$proSku));
        
        Mage::log('request',null,'bhargav.log',true);
        Mage::log($form->toHtml(),null,'bhargav.log',true);
        
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to E-Billing Solutions in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">		
		document.forms["innoviti_payment_checkout"].submit();
		</script>';
        $html.= '</body></html>';
        return $html;
    }
}
