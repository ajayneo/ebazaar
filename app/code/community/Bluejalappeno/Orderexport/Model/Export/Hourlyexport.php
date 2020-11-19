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
class Bluejalappeno_Orderexport_Model_Export_Hourlyexport extends Bluejalappeno_Orderexport_Model_Export_Abstractcsvnav
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
    protected $stockLocationModelRaw;
    protected $order;
    protected $product;
    protected $customerModel;
    protected $serNo1 = 1;
    protected $serNo2;

    public function __construct()
    {
        $this->order = Mage::getModel('sales/order');
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
    public function exportOrders($orders,$from,$to) 
    {    
        ini_set('max_execution_time', 0);
        //$fileName = 'hourly_sales_report_'.$from.'_to_'.$to.'.csv';

        $from_hour = date('ha',strtotime($from));
        $to_hour = date('ha',strtotime($to));
        $file_name = (string) "orders_".$from_hour."_to_".$to_hour.".csv";
        $openbox = array(60,94);
        $new_cats = array(76,89);
        $prewoned_cats = array(11,97);
        $accessories = array(7);
        $label_array = array('Openbox' => $openbox, 'Preowned'=>$prewoned_cats, 'New'=>$new_cats, 'Accessories'=>$accessories);
        
        $filepath = Mage::getBaseDir().'/kaw17842knlpREGdasp9045/navision/export'.'/'.$file_name;
        $this->serNo2 = end($orders);
        $fp = fopen($filepath, 'w');
        $this->writeHeadRow($fp);
        foreach ($orders as $order_id) {
            $order = Mage::getModel('sales/order')->load($order_id); 
            $this->writeOrder($order, $fp);

//custom code;
            $items = $order->getItemsCollection();
            foreach ($items as $key => $item){
                $item_id = $item->getItemId();
                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                $categories = $_product->getCategoryIds();
                foreach ($label_array as $cat_name => $value) {
                    $inter = array_intersect($categories, $value);
                    
                    if(count($inter) >= 1){
                       $products_cat[$cat_name][$order_id][$item_id]['product_id'] = $item->getProductId();
                       $products_cat[$cat_name][$order_id][$item_id]['qty'] = $item->getQtyOrdered();
                       $products_cat[$cat_name][$order_id][$item_id]['price'] = $item->getBaseRowTotalInclTax();
                    }

                }
            }
        }   

        fclose($fp);

        //return $file_name;
        return array('filename'=>$file_name,'cats'=>$products_cat);
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
        $common = $this->getCommonOrderValues($order);

        $orderItems = $order->getItemsCollection();
        $order_invoice_data = array('','');
        /*Add Invoice Increment Id*/
        if ($order->hasInvoices()) {
            $invIncrementId = array();
            foreach ($order->getInvoiceCollection() as $invoice) {

                $itemInvoiceSerial = $invoice->getItemsCollection()->addFieldToSelect('serial');

                $itemInvoiceSerialNo[] = $itemInvoiceSerial->getData(); 
                //print_r($invoice->getData());exit;
                $invoiceIncId[] = $invoice->getIncrementId();
                //$invoiceIncId[] = $invoice->getCreatedAt();
            }
            if(!empty($invoiceIncId)) {
                $order_invoice_data = false;
                if(count($invoiceIncId) > 1){
                    $order_invoice_id_cs = implode('|',$invoiceIncId);
                }else{
                    $order_invoice_id_cs = $invoiceIncId;
                }
                $order_invoice_data = array($order_invoice_id_cs,$invoice->getCreatedAt());
            }
        }



        #print_r($order_invoice_ids);die;
        /*End Add Invoice Increment Id*/
        $itemInc = 0;

        foreach ($itemInvoiceSerialNo[0] as $key => $value){
            $serialNumber[] = substr($value['serial'],0,50); 
        }

        if($this->getPaymentMethod($order) == 'banktransfer'){

            $collectionDelivery = Mage::getModel("banktransferdelivery/delivery")->getCollection()->addFieldToSelect('delivery')->addFieldToFilter('order_num',$order->getRealOrderId())->getFirstItem()->getData();

            $da = $collectionDelivery['delivery'] + 5;
                    
            $date = explode(' ',$order->getCreatedAt());
            $NewDays = date ("d-M-Y", strtotime ($date[0] ."+".$da." days")); 

            $credit     = $collectionDelivery['delivery'];
            $dueDate    = '';
        }

        
        $serNo = 0; 
         
        foreach ($orderItems as $item)
        {
            $collectionAffilateOrders = Mage::getModel('neoaffiliate/affiliate')->getCollection()->addFieldToFilter('order_id',$item->getOrderId())->getFirstItem()->getData();

            $invData[0] = $order_invoice_data[0];

            $order_invoice_data1 = explode(' ',$order_invoice_data[1]);

            $invData[1] = $order_invoice_data1[0];
            $invData[2] = $order_invoice_data1[1];

            $dateInvoice = $invData[1];

            if($dateInvoice)
            {
                $dateInv = explode('-', $dateInvoice);

                $dateObj   = DateTime::createFromFormat('!m', $dateInv[1]);
                $monthName = substr($dateObj->format('m'), 0,3);  

                $invData[1] = $dateInv[2].'-'.$monthName.'-'.$dateInv[0];

            }

            
 

            if (!$item->isDummy()) {
                $custom_array = array_merge($common,$this->getOrderItemValues($item, $order, ++$itemInc));
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc),$invData);
                
                $record[] = $this->getStockLocation($record[0]);
                $record[] = $this->getBrand($item->getProductId()); 

                $record[] = $this->getShippedItem($item->getQtyShipped(),$item->getPriceInclTax(),$item->getDiscountAmount());               
                $record[] = $this->getUserAgent($item->getOrderId());   

                $record[] = $serialNumber[$serNo]; 

                $record[] = $credit; 
                $record[] = ''; 

                if($collectionAffilateOrders['iscstused']){              
                    $record[] = $collectionAffilateOrders['iscstused'];   
                }else{
                    $record[] = 'No';
                }  


                if($this->serNo2 != $item->getOrderId())  
                {
                    //$record[] = 'newline'; 
                    $this->serNo1 = $this->serNo1 + 1;  
                }  

                //$record[] = 'newline';   

                $serNo++;             

                fputcsv($fp, $custom_array, self::DELIMITER, self::ENCLOSURE); 
                // fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE); 
            } 
        }
    }

    /**
     * Returns the head column names.
     *
     * @return Array The array containing all column names
     */
    protected function getHeadRowValues()
    {
        
        return array('Order Id',
            'Store Name',
            'Product',
            'Category',
            'Total Sale (Inc tax)',
            'Qty',
            'Product Price (Inc tax)');
       
    }

    /**
     * Returns the values which are identical for each row of the given order. These are
     * all the values which are not item specific: order data, shipping address, billing
     * address and order totals.
     *
     * @param Mage_Sales_Model_Order $order The order to get values from
     * @return Array The array containing the non item specific values
     */
    protected function getCommonOrderValues($order)
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
        $stringDate = date('d/m/Y', strtotime($createdAt));

        //echo $date = Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', true);exit;;
        
        //$code = $this->stateCodeMapping($shippingAddress->getRegionCode()); 
        //$code1 = $this->stateCodeMapping($billingAddress->getRegionCode()); 
        return array(
            $order->getRealOrderId(),
            substr($order->getCustomerName(),0,50)
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
        $main_cat = '';
        foreach($categories as $category) {
            $obj = "category".$i;
            $$obj = $category->getName();
            if(in_array($category->getName(), array('New','Open Box','Pre Owned','Accessories')) === TRUE){
                $main_cat = $category->getName();
            }
            $i++;
            //var_dump($category->getName());
        }

         return array(
            str_replace(',', ' ',$item->getName()),
            $main_cat,
            $item->getBaseRowTotalInclTax(),
            (int)$item->getQtyOrdered(),
            $item->getBasePriceInclTax(),
            );
         ;
        
    }
    
}
?>