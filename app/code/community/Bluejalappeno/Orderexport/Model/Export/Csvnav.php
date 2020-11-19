<?php
/**
 * Magento Bluejalappeno Order Export Module
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Bluejalappeno
 * @package    Bluejalappeno_OrderExport
 * @copyright  Copyright (c) 2010 Wimbolt Ltd (http://www.bluejalappeno.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Genevieve Eddison <sales@bluejalappeno.com>
 * */
class Bluejalappeno_Orderexport_Model_Export_Csvnav extends Bluejalappeno_Orderexport_Model_Export_Abstractcsv
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
    protected $stockLocationModelRaw;
    protected $order;
    protected $product;
    protected $customerModel;

    public function __construct()
    {
        $this->order = Mage::getModel('sales/order');
        $this->invoice = Mage::getModel('sales/order_invoice');
        $this->stockLocationModelRaw = Mage::getModel('stocklocation/location');
        $this->product = Mage::getModel('catalog/product');
        $this->customerModel = Mage::getModel('customer/group'); 
    } 

    /**
     * Concrete implementation of abstract method to export given orders to csv file in var/export.
     *
     * @param $orders List of orders of type Mage_Sales_Model_Order or order ids to export.
     * @return String The name of the written csv file in var/export
     */
    public function exportOrders($orders)
    {
        ini_set('max_execution_time', 0);
        //name edited for getting user name
        if(Mage::getSingleton('admin/session')){
            $user = Mage::getSingleton('admin/session'); 
            $userName = $user->getUser()->getUsername();
        }
        $fileName = 'nav_export_'.$userName.'_'.date("Ymd_His").'.csv'; 
        // $fileName = 'order_export_'.date("Ymd_His").'.csv';
        $fp = fopen(Mage::getBaseDir('export').'/'.$fileName, 'w');  

        $this->writeHeadRow($fp);  
        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);

        return $fileName;
    }   

    /**
     * Writes the head row with the column names in the csv file.
     *
     * @param $fp The file handle of the csv file
     */
    protected function writeHeadRow($fp)
    {
        fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
    }

    /**
     * Writes the row(s) for the given order in the csv file.
     * A row is added to the csv file for each ordered item.
     *
     * @param Mage_Sales_Model_Order $order The order to write csv of
     * @param $fp The file handle of the csv file
     */
    protected function writeOrder($order, $fp)
    {
        ini_set('max_execution_time', 0);
        //$common = $this->getCommonOrderValues($order);
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        //$orderItems = $order->getItemsCollection();

        $orderItems = Mage::getSingleton('sales/order_item')->getCollection()->setOrderFilter($order);
        $orderItems->getSelect()->joinLeft(
            array('oii' => $orderItems->getTable('sales/invoice_item')),
            'main_table.item_id = oii.order_item_id',
            array('invoice_id' => 'parent_id')
        );
        /*$orderItems->getSelect()->joinLeft(
            array('ost' => 'sales_flat_shipment'), //$orderItems->getTable('sales/order_shipment')
            'main_table.order_id = ost.order_id',
            array('shipment_id' => 'entity_id','order_id as shipment_order_id')
        );*/
        $orderItems->getSelect()->group('main_table.item_id');

        $order_invoice_data = array('','');
         $gstin = '';
        if($shippingAddress){
            $gstin = $shippingAddress->getGstin();
        }

        $bill_to_gstin = '';
        if($order->getBillingAddress()){
            $bill_to_gstin = $order->getBillingAddress()->getGstin();
        }

        if($gstin == '' && $bill_to_gstin == ''){
            $_customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if($_customer->getGstin()){
                $gstin = $_customer->getGstin();
                $bill_to_gstin = $_customer->getGstin();
            }
        }
        /*Add Invoice Increment Id*/
        if ($order->hasInvoices()) {
            $invIncrementId = array();$order_invoice_data = array();
            foreach ($order->getInvoiceCollection() as $invoice) {

                $itemInvoiceSerial = $invoice->getItemsCollection()->addFieldToSelect('serial');

                $itemInvoiceSerialNo[] = $itemInvoiceSerial->getData(); 
                //print_r($invoice->getData());exit;
                $invoiceIncId[] = $invoice->getIncrementId();
                //$invoiceIncId[] = $invoice->getCreatedAt();
                $order_invoice_data[] = $invoice->getIncrementId().",".$invoice->getCreatedAt();
               
            }
           /* if(!empty($invoiceIncId)) {
                $order_invoice_data = false;
                $order_invoice_id_cs = implode('|',$invoiceIncId);
                $order_invoice_data = array($order_invoice_id_cs,$invoice->getCreatedAt());
            }*/
        }


        // print_r($itemInvoiceSerialNo);




        // print_r($order_invoice_ids);
        /*End Add Invoice Increment Id*/
        $itemInc = 0;
// print_r($itemInvoiceSerialNo[0]);
        foreach ($itemInvoiceSerialNo[0] as $key => $value){
            // print_r($value['serial']);
            // echo $imei = preg_replace('/\s+/', '|', $value['serial']);
// echo "<br>".$imei_string = trim(preg_replace('/\s+/g', '|', $value['serial']));
            $serialNumber[] = substr($value['serial'],0,50); 
            // $serialNumber[] = preg_replace('/\s+/', '|', $value['serial']);
        }
        // die('die here');

        if($this->getPaymentMethod($order) == 'banktransfer'){

            $collectionDelivery = Mage::getModel("banktransferdelivery/delivery")->getCollection()->addFieldToSelect('delivery')->addFieldToFilter('order_num',$order->getRealOrderId())->getFirstItem()->getData();

            $da = $collectionDelivery['delivery'] + 5;
                    
            $date = explode(' ',$order->getCreatedAt());
            $NewDays = date ("d-M-Y", strtotime ($date[0] ."+".$da." days")); 

            $credit     = $collectionDelivery['delivery'];
            $dueDate    = $NewDays;
        }

        
        $serNo = 0; 
        // $invoice_increment = 0;
        // $invoice_date = 0;
        // $invoice_time = 0;
        foreach ($orderItems as $item)
        {

            

            $collectionAffilateOrders = Mage::getModel('neoaffiliate/affiliate')->getCollection()->addFieldToFilter('order_id',$item->getOrderId())->getFirstItem()->getData();
           // echo $item->getTrackingId();die;
            $invoice = $this->invoice->load($item->getInvoiceId());
            
           // print_r($order_invoice_data);
            $order_invoice_data = explode(',',$order_invoice_data);
            //$invData[0] = $order_invoice_data[0];
            $order_invoice_data1 = explode(' ',$invoice->getCreatedAt());

            //$invData[1] = $invoice->getCreatedAt();
            $invoice_increment = '';
            $invoice_date = '';
            $invoice_time = '';
            $invoiceId = '';
            $invData = array();
            if($item->getInvoiceId()){
                $invoiceId = $invoice->getId();
                $invoice_increment = $invoice->getIncrementId();
                $invoice_date = $order_invoice_data1[0];
                $invoice_time= $order_invoice_data1[1];
            }
                $invData[0] = $invoice_increment;
                $invData[1] = $invoice_date;
                $invData[2] = $invoice_date;


            

            $dateInvoice = $invData[1];

            /*if($dateInvoice)
            {
                $dateInv = explode('-', $dateInvoice);

                $dateObj   = DateTime::createFromFormat('!m', $dateInv[1]);
                $monthName = substr($dateObj->format('m'), 0,3);  

                $invData[1] = $dateInv[2].'-'.$monthName.'-'.$dateInv[0];

            }*/


            
            //echo $invData[2] = $this->getStockLocation($item->getOrderId(),$invoice->getIncrementId());
            //$invData[1] = $invoice->getCreatedAt();


            $common = $this->getCommonOrderValues($order,$invoiceId);

            // if (!$item->isDummy()) { //to add non visible individually products with bundle or configurable
                
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc),$invData);
                //echo $record[0]."---".$invData[0];die;
                //echo $record[0]."--".$invoice->getIncrementId();
                $record[] = $this->getStockLocation($record[0],$invoice->getIncrementId());
                $record[] = $this->getBrand($item->getProductId()); 

                $record[] = $this->getShippedItem($item->getQtyShipped(),$item->getPriceInclTax(),$item->getDiscountAmount());               
                $record[] = $this->getUserAgent($item->getOrderId());   

                $record[] = $serialNumber[$serNo]; 

                $record[] = $credit; 
                $record[] = $dueDate; 

                if($collectionAffilateOrders['iscstused']){              
                    $record[] = $collectionAffilateOrders['iscstused'];   
                }else{
                    $record[] = 'No';
                }     

                $record[] = $gstin;
                $record[] = $bill_to_gstin;

                $serNo++;             

                fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
            // } 
        }
    }

    /**
     * Returns the head column names.
     *
     * @return Array The array containing all column names
     */
    protected function getHeadRowValues()
    {
        return array(
            'Order Number',
            'Order Date',
            'Order Time',
            'Order Status',
            'Order Purchased From',
            'Order Payment Method',
            'Credit Card Type',
            'Order Shipping Method', 
            'Order Subtotal',
            'Order Tax',
            'Order Shipping', 
            'Order Discount', 
            'Order Grand Total',
            'Order Base Grand Total',
            'Order Paid',
            'Order Refunded',
            'Order Due',
            'Total Qty Items Ordered',
            'Tracking Number',
            'Tracking Partner Name',
            'Customer Name',
            'Customer Group',
            'Customer Email',
            'Repcode',
            'Asm Map',
            'Shipping Name',
            'Shipping Company',
            'Shipping Street',
            'Shipping Zip',
            'Shipping City',
            'Shipping State',
            'Shipping State Name',
            //'Shipping Country',
            //'Shipping Country Name',
            'Shipping Phone Number',
            'FAX Number',
            'Billing Name',
            'Billing Company',
            'Billing Street',
            'Billing Zip',
            'Billing City',
            'Billing State',
            'Billing State Name',
            //'Billing Country',
            //'Billing Country Name',
            'Billing Phone Number',
            'FAX Number',
            'Order Item Increment',
            'Item Name',
            'Item Status',
            'Item SKU',
            'Item Options',
            'Item Original Price',
            'Item Price',
            'Item Qty Ordered',
            'Item Qty Invoiced',
            'Item Qty Shipped',
            'Item Qty Canceled',
            'Item Qty Refunded',
            'Item Tax',
            'Item Discount',
            'Item Total',
            'Item Category1',
            'Item Category2',
            'Item Category3',
            'Item Category4',
            'Invoice Number',
            'Invoice Date',
            'Invoice Time', 
            'Warehouse Location',
            'Brand',
            'Shipped Value',
            'User Agent',
            'Serial No',
            'Credit Days',
            'Payment Due On',
            'Is CST Used',
            'GST IN',
            'Bill to GST IN'
        ); 
    }

    /*
    *
    *
    */
    function getTrackingInformation($order_id,$invoice_id,$returnValue){

        //$shipmentCollection = $order->getShipmentsCollection();
        //foreach($shipmentCollection as $shipment){
            //$shipmentIncrementId = $shipment->getIncrementId();
            $track=Mage::getModel('sales/order_shipment_track')->getCollection()
            ->addFieldToFilter('order_id',$order_id)->addFieldToFilter('invoice_id',$invoice_id)->getData();
            
             if($returnValue == 'title'){
                return $track[0]['title'];
             }
             if($returnValue == 'number'){
                return $track[0]['track_number'];
             }
            
        //}
    }

    /**
     * Returns the values which are identical for each row of the given order. These are
     * all the values which are not item specific: order data, shipping address, billing
     * address and order totals.
     *
     * @param Mage_Sales_Model_Order $order The order to get values from
     * @return Array The array containing the non item specific values
     */
    protected function getCommonOrderValues($order,$invoice_id)
    {
        ini_set('max_execution_time', 0);   
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
        $trackingNumber = '';
        $trackingParterName = '';
        /*get Tracking number and Parter Name*/

        $shipmentCollection = $order->getShipmentsCollection();
        foreach($shipmentCollection as $shipment){
            $shipmentIncrementId = $shipment->getIncrementId();
            $shipment=Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
            foreach ($shipment->getAllTracks() as $track) {
               //print_r($track->getData());
               $trackingNumber = $track->getTrackNumber();
               $trackingParterName = $track->getTitle();
            }
        }
        /*End Tracking*/
        $statuses = Mage::getModel('sales/order_status')
            ->getCollection()
            ->addFieldToSelect('status')
            ->addFieldToSelect('label')
            ->addFieldToFilter('status',array('eq'=>$order->getStatus()));
        #print_r($statuses->getData());die;
        $status_array = $statuses->getData();

        $customerData = Mage::getModel('customer/customer')->load($order->getCustomerId())->getData();
        $objAffiliatescontacts = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();

        $createdAt = $order->getCreatedAt();
        //$stringDate = date('d / m / Y', strtotime($createdAt));

        $stringDate = Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', true);
        
        $code = $this->stateCodeMapping($shippingAddress->getRegionCode()); 
        $code1 = $this->stateCodeMapping($billingAddress->getRegionCode()); 

        return array(
            $order->getRealOrderId(),
            $stringDate,
            substr(Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', true),-11), 
            substr($status_array[0]['label'],0,50),
            substr($this->getStoreName($order),0,50),
            $this->getPaymentMethod($order),
            $this->getCcType($order),
            $this->getShippingMethod($order),
            $order->getData('subtotal'),
            $order->getData('tax_amount'),
            $order->getData('shipping_amount'),
            ltrim($order->getData('discount_amount'), '=-'),
            $order->getData('grand_total'), 
            $order->getData('base_grand_total'),
            $order->getData('total_paid'),
            $order->getData('total_refunded'),
            $order->getData('total_due'),
            $this->getTotalQtyItemsOrdered($order),
            $this->getTrackingInformation($order->getId(),$invoice_id,'number'),//substr($trackingNumber,0,50),
            $this->getTrackingInformation($order->getId(),$invoice_id,'title'), //substr($trackingParterName,0,50),
            substr($order->getCustomerName(),0,50),
            substr($this->customerModel->load($order->getCustomerGroupId())->getCustomerGroupCode(),0,50),
            $order->getCustomerEmail(),
            substr($customerData['repcode'],0,50),
            substr($objAffiliatescontacts->getOptionText($customerData['asm_map']),0,50),
            substr($shippingAddress ? $shippingAddress->getName() : '',0,50),
            substr($shippingAddress ? $shippingAddress->getData("company") : '',0,50),
            str_replace(',', ' ', substr($shippingAddress ? $this->getStreet($shippingAddress) : '',0,50)),
            substr($shippingAddress ? $shippingAddress->getData("postcode") : '',0,50),
            substr($shippingAddress ? $shippingAddress->getData("city") : '',0,50),
            $code, 
            substr($shippingAddress ? $shippingAddress->getRegion() : '',0,50),
            //$shippingAddress ? $shippingAddress->getCountry() : '',
            //$shippingAddress ? $shippingAddress->getCountryModel()->getName() : '',
            substr($shippingAddress ? $shippingAddress->getData("telephone") : '',0,50),
            substr($shippingAddress ? $this->getFaxNo($order->getRealOrderId()) : '',0,50),
            substr($billingAddress->getName(),0,50),
            substr($billingAddress->getData("company"),0,50),
            str_replace(',', ' ',substr($this->getStreet($billingAddress),0,50)),
            substr($billingAddress->getData("postcode"),0,50),
            substr($billingAddress->getData("city"),0,50),
            $code1,
            substr($billingAddress->getRegion(),0,50),
            //$billingAddress->getCountry(),
            //$billingAddress->getCountryModel()->getName(),
            substr($billingAddress->getData("telephone"),0,50),
            substr($this->getFaxNo($order->getRealOrderId()),0,50)
        );
    }

    /**
     * Returns the item specific values.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to get values from
     * @param Mage_Sales_Model_Order $order The order the item belongs to
     * @return Array The array containing the item specific values
     */
    protected function getOrderItemValues($item, $order, $itemInc=1)
    {
        $category1 = '';
        $category2 = '';
        $category3 = '';
        $category4 = '';
        $product = $item->getProduct();
        $categories = $product->getCategoryCollection()->addAttributeToSelect('name');
        $i=1;
        foreach($categories as $category) {
            $cat_name = $category->getName();
            $cat_name = str_replace("ASIS","A",$cat_name);
            $cat_name = str_replace("Refurbished","Refurb",$cat_name);
            
            if($cat_name !== 'Smartphones'){
                $obj = "category".$i;
                $$obj = $cat_name;
                $i++;
                //var_dump($category->getName());
            }
        }

        $item_price = (int) $this->getItemTotal($item) / (int)$item->getQtyOrdered();
        $sku_array = explode("/", $this->getItemSku($item));
        $sku = $sku_array[0];
        return array(
            $itemInc,
            str_replace(',', ' ',$item->getName()),
            // $item->getStatus(),
            $sku,
            $this->getItemSku($item),
            $this->getItemOptions($item),
            //$item->getOriginalPrice(),
            // (int) round(Mage::helper('tax')->getPrice($product, $product->getPrice())),
            // (int) round(Mage::helper('tax')->getPrice($item, $item->getPrice())),
            $item_price,
            $item->getData('price'),
            (int)$item->getQtyOrdered(),
            (int)$item->getQtyInvoiced(),
            (int)$item->getQtyShipped(),
            (int)$item->getQtyCanceled(),
            (int)$item->getQtyRefunded(),
            $item->getTaxAmount(),
            $item->getDiscountAmount(),
            $this->getItemTotal($item),
            $category1,
            $category2,
            $category3,
            $category4
        );
    }
    
    protected function getStockLocation($incrementId,$invoiceIncrementId)
    {
        /*$order = $this->order->load($incrementId, 'increment_id');
        $orderId = $order->getId();
        $stockLocationModelRaw = $this->stockLocationModelRaw;
        $stockLocation = $stockLocationModelRaw->getCollection()->addFieldToSelect(array('stock_location','id'))->addFieldToFilter('order_id',$orderId)->getFirstItem()->getData();
        if(!empty($stockLocation['stock_location']))
        {
            return $stockLocation['stock_location'];
        }
        else 
        {
            return '';
        }*/

        
        $order = $this->order->load($incrementId, 'increment_id');
        $orderIdn = $order->getId();

        $invoice = $this->invoice->load($invoiceIncrementId, 'increment_id');
        $invoiceIdn = $invoice->getId();
        $stockLocationModel = Mage::getModel('stocklocation/location')->getCollection()->addFieldToSelect('*')->addFieldToFilter('order_id',$orderIdn);
        if(in_array(null,$stockLocationModel->getColumnValues('invoice_id'))){
            $stockLocationModel = $stockLocationModel->getData();
             return $stockLocationModel[0]['stock_location'];  
        }else{
            foreach ($stockLocationModel as $stocklocation) {
                $warehouse[$stocklocation->getInvoiceId()] = $stocklocation->getStockLocation();
            }
            return $warehouse[$invoice->getId()];
        }
    }
    
    protected function getBrand($prodictId)
    {
        $product = $this->product->load($prodictId);
        return $manufacturerName = $product->getAttributeText('brands'); 
    }

    protected function getUserAgent($id)
    {                   
        $order = $this->order->load($id);              
        
        $result = '';

        if(strlen($order->getRemoteIp()) > 0) { 
            $result = 'Website';
        } else {
            $result = 'Application';
        }
        
        
        
        unset($order);
        return $result;
    }

    protected function getShippedItem($x,$y,$z)
    {
        $qty = (int)$x;
        $price = (int)$y;
        $finalPrice = 0;
        $dis = (int) $z;
        for($i=1;$i<=$qty;$i++)
        {
            $finalPrice++;
        }

        $priceFin = $price*$finalPrice;
 
        $priceFin1 = $priceFin -  $dis;
 
        return  $priceFin1;  
    }
    
    protected function getFaxNo($orderId)
    {
        $order = $this->order->load($orderId,'increment_id');

        //If they have no customer id, they're a guest.
        if($order->getCustomerId() === NULL){
            if(is_object($order->getDefaultBillingAddress()))  
            {
                return $order->getDefaultBillingAddress()->getFax();
            }
            else  
            {
                return 'Empty';
            }  
             
        } else {
            //else, they're a normal registered user.
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            if(is_object($customer->getDefaultBillingAddress()))
            {
                return $customer->getDefaultBillingAddress()->getFax();
            }
            else 
            {
                return 'Empty';
            }  
        }
    }

    protected function stateCodeMapping($code)
    {
        if($code=='IN-AP'){
            return 'ANP';
        }elseif($code=='IN-AR'){
            return 'ANP';
        }elseif($code=='IN-AS'){
            return 'AS';
        }elseif($code=='IN-BR'){
            return 'BIH';
        }elseif($code=='IN-CH'){
            return 'CH';
        }elseif($code=='IN-CG'){
            return 'CG';
        }elseif($code=='IN-DN'){
            return 'DAD';
        }elseif($code=='IN-DD'){
            return 'DD';
        }elseif($code=='IN-DL'){
            return 'ND';
        }elseif($code=='IN-GA'){
            return 'GA';
        }elseif($code=='IN-GJ'){
            return 'GUJ';
        }elseif($code=='IN-HR'){
            return 'HR';
        }elseif($code=='IN-HP'){
            return 'HP';
        }elseif($code=='IN-JK'){
            return 'JK';
        }elseif($code=='IN-JH'){
            return 'JH';
        }elseif($code=='IN-KA'){
            return 'KAR';
        }elseif($code=='IN-KL'){
            return 'KER';
        }elseif($code=='IN-LD'){
            return 'LA';
        }elseif($code=='IN-MP'){
            return 'MP';
        }elseif($code=='IN-MH'){
            return 'MAH';
        }elseif($code=='IN-MN'){
            return 'MAN';
        }elseif($code=='IN-ML'){
            return 'MEG';
        }elseif($code=='IN-MZ'){
            return 'MIZ';
        }elseif($code=='IN-NL'){
            return 'NAG';
        }elseif($code=='IN-OR'){
            return 'ORI';
        }elseif($code=='IN-PY'){
            return 'PON';
        }elseif($code=='IN-PB'){
            return 'PUN';
        }elseif($code=='IN-RJ'){
            return 'RAJ';
        }elseif($code=='IN-SK'){
            return 'SIK';
        }elseif($code=='IN-TN'){
            return 'TN';
        }elseif($code=='IN-TR'){
            return 'TRI';
        }elseif($code=='IN-UP'){
            return 'UP';
        }elseif($code=='UTTAR'){
            return 'UT';
        }elseif($code=='IN-WB'){
            return 'WB';
        }elseif($code=='Andaman Nicobar'){
            return 'AN';
        }elseif($code=='Dadra Nagar Haveli'){
            return 'DAD';
        }elseif($code=='Daman Diu'){
            return 'DD';
        }elseif($code=='Pondicherry'){
            return 'PON';
        }elseif($code=='IN-TS'){
            return 'TL';
        }elseif($code=='IN-UK'){
            return 'UK';
        }
    }
}
?>