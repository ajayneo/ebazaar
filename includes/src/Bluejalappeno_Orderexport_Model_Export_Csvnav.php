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
        $fileName = 'order_export_'.date("Ymd_His").'.csv';
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
                $order_invoice_id_cs = implode('|',$invoiceIncId);
                $order_invoice_data = array($order_invoice_id_cs,$invoice->getCreatedAt());
            }
        }



        #print_r($order_invoice_ids);die;
        /*End Add Invoice Increment Id*/
        $itemInc = 0;

        foreach ($itemInvoiceSerialNo[0] as $key => $value){
            $serialNumber[] = $value['serial'];
        }
        
        $serNo = 0;
        foreach ($orderItems as $item)
        {
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
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc),$invData);
                
                $record[] = $this->getStockLocation($record[0]);
                $record[] = $this->getBrand($item->getProductId()); 

                $record[] = $this->getShippedItem($item->getQtyShipped(),$item->getPriceInclTax(),$item->getDiscountAmount());               
                $record[] = $this->getUserAgent($item->getOrderId());   

                $record[] = $serialNumber[$serNo]; 

                $serNo++;             

                fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
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
            'Serial No' 
        );
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
        $stringDate = date('d / m / Y', strtotime($createdAt));

        //echo $date = Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', true);exit;
        
        
        return array(
            $order->getRealOrderId(),
            $stringDate,
            substr(Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', true),-11), 
            $status_array[0]['label'],
            $this->getStoreName($order),
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
            $trackingNumber,
            $trackingParterName,
            $order->getCustomerName(),
            $this->customerModel->load($order->getCustomerGroupId())->getCustomerGroupCode(),
            $order->getCustomerEmail(),
            $customerData['repcode'],
            $objAffiliatescontacts->getOptionText($customerData['asm_map']),
            $shippingAddress ? $shippingAddress->getName() : '',
            $shippingAddress ? $shippingAddress->getData("company") : '',
            $shippingAddress ? $this->getStreet($shippingAddress) : '',
            $shippingAddress ? $shippingAddress->getData("postcode") : '',
            $shippingAddress ? $shippingAddress->getData("city") : '',
            $shippingAddress ? $shippingAddress->getRegionCode() : '',
            $shippingAddress ? $shippingAddress->getRegion() : '',
            //$shippingAddress ? $shippingAddress->getCountry() : '',
            //$shippingAddress ? $shippingAddress->getCountryModel()->getName() : '',
            $shippingAddress ? $shippingAddress->getData("telephone") : '',
            $shippingAddress ? $this->getFaxNo($order->getRealOrderId()) : '',
            $billingAddress->getName(),
            $billingAddress->getData("company"),
            $this->getStreet($billingAddress),
            $billingAddress->getData("postcode"),
            $billingAddress->getData("city"),
            $billingAddress->getRegionCode(),
            $billingAddress->getRegion(),
            //$billingAddress->getCountry(),
            //$billingAddress->getCountryModel()->getName(),
            $billingAddress->getData("telephone"),
            $this->getFaxNo($order->getRealOrderId())
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
            $obj = "category".$i;
            $$obj = $category->getName();
            $i++;
            //var_dump($category->getName());
        }
        return array(
            $itemInc,
            $item->getName(),
            $item->getStatus(),
            $this->getItemSku($item),
            $this->getItemOptions($item),
            $item->getOriginalPrice(),
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
    
    protected function getStockLocation($incrementId)
    {
        $order = $this->order->load($incrementId, 'increment_id');
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
}
?>