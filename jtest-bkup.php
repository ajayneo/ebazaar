<?php 
require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();

// $_order = Mage::getModel('sales/order')->load(121051);
// $_order->setPaymentMethod('banktransfer');
// $_order->save();

// COD cashondelivery
// Credit Days Payment banktransfer
// Credit/Debit Card or Netbanking billdesk_ccdc_net
// Advanced Payment custompayment

//save tracking number
// $shipmentId = '300082390';
// $trackmodel = Mage::getModel('sales/order_shipment_api')->addTrack($shipmentId,'dhl','DHL','3674504');
// $trackmodel->save();

//save payment method
$orderId = '200111789'; // Incremented Order Id
$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
$order->setData('state','new');
$order->setStatus('pending'); 
// $payment = $order->getPayment();
// $payment->setMethod('cashondelivery'); // Assuming 'test' is updated payment method
// $payment->save();
$order->save();


exit();
//get product list.
$product_list = array();

//add product sku.
$product_list = array('NEWNBK00176','NEWNBK00175','NEWNBK00174');

//print_r($product_list);

foreach ($product_list as $pro_sku) {        	

$_catalog = Mage::getModel('catalog/product');
$_productId = $_catalog->getIdBySku($pro_sku);
$_product = Mage::getModel('catalog/product')->load($_productId);
//echo $_product->getProductUrl(true);exit;
//echo '<pre>', print_r($_product);exit;

echo'<tr>
	<td style="font-family: arial; font-size: 14px; color: #424242; border-bottom: 1px solid #424242; 
	border-right: 1px solid #424242; padding: 10px 20px; line-height: 20px;">
	<a href="'.$_product->getProductUrl(true).'?utm_source=Emailer&amp;utm_medium=email&amp;utm_campaign=17JAN18&amp;utm_content=Brand-New-Apple-ipad" 
	target="_blank" style="text-decoration: none; color: #424242">
	<span style="color: #424242;">'.$_product->getName().'</span>
	</a>
	</td>
	<td width="25%" align="center" style="font-family: arial; font-size: 14px; color: #424242; border-bottom: 1px solid #424242; border-right: 1px solid #424242; padding: 10px 20px; line-height: 20px;">
	 <a href="'.$_product->getProductUrl(true).'?utm_source=Emailer&amp;utm_medium=email&amp;utm_campaign=17JAN18&amp;utm_content=Brand-New-Apple-ipad"
	 target="_blank" style="text-decoration: none; color: #424242"><span style="color: #424242;">'.$_product->getPrice().'/-</span></a>
	</td>
	</tr>';

echo '<br />';

}

?>