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
class Bluejalappeno_Orderexport_Model_Export_Csv extends Bluejalappeno_Orderexport_Model_Export_Abstractcsv
{
    const ENCLOSURE = '"';
    const DELIMITER = ',';
    protected $stockLocationModelRaw;
    protected $order;

    public function __construct()
    {
        $this->order = Mage::getModel('sales/order');
        $this->stockLocationModelRaw = Mage::getModel('stocklocation/location');
    }
    /**
     * Concrete implementation of abstract method to export given orders to csv file in var/export.
     *
     * @param $orders List of orders of type Mage_Sales_Model_Order or order ids to export.
     * @return String The name of the written csv file in var/export
     */
    public function exportOrders($orders)
    {
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
        $common = $this->getCommonOrderValues($order);

        $orderItems = $order->getItemsCollection();
		$order_invoice_data = array('N/A','N/A');
		/*Add Invoice Increment Id*/
		if ($order->hasInvoices()) {
			$invIncrementId = array();
			foreach ($order->getInvoiceCollection() as $invoice) {
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
        foreach ($orderItems as $item)
        {
            if (!$item->isDummy()) {
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc),$order_invoice_data);
                
                $record[] = $this->getStockLocation($record[0]);

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
            'Customer Email',
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
			'Warehouse Location'
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
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
		$trackingNumber = 'N/A';
		$trackingParterName = 'N/A';
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
		
        return array(
            $order->getRealOrderId(),
            Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', true),
            //$order->getStatus(),
			$status_array[0]['label'],
            $this->getStoreName($order),
            $this->getPaymentMethod($order),
            $this->getCcType($order),
            $this->getShippingMethod($order),
            $this->formatPrice($order->getData('subtotal'), $order),
            $this->formatPrice($order->getData('tax_amount'), $order),
            $this->formatPrice($order->getData('shipping_amount'), $order),
            $this->formatPrice($order->getData('discount_amount'), $order),
            $this->formatPrice($order->getData('grand_total'), $order),
            $this->formatPrice($order->getData('base_grand_total'), $order),
            $this->formatPrice($order->getData('total_paid'), $order),
            $this->formatPrice($order->getData('total_refunded'), $order),
            $this->formatPrice($order->getData('total_due'), $order),
            $this->getTotalQtyItemsOrdered($order),
			$trackingNumber,
			$trackingParterName,
            $order->getCustomerName(),
            $order->getCustomerEmail(),
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
            $billingAddress->getName(),
            $billingAddress->getData("company"),
            $this->getStreet($billingAddress),
            $billingAddress->getData("postcode"),
            $billingAddress->getData("city"),
            $billingAddress->getRegionCode(),
            $billingAddress->getRegion(),
            //$billingAddress->getCountry(),
            //$billingAddress->getCountryModel()->getName(),
            $billingAddress->getData("telephone")
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
		$category1 = 'N/A';
		$category2 = 'N/A';
		$category3 = 'N/A';
		$category4 = 'N/A';
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
            $this->formatPrice($item->getOriginalPrice(), $order),
            $this->formatPrice($item->getData('price'), $order),
            (int)$item->getQtyOrdered(),
            (int)$item->getQtyInvoiced(),
            (int)$item->getQtyShipped(),
            (int)$item->getQtyCanceled(),
        	(int)$item->getQtyRefunded(),
            $this->formatPrice($item->getTaxAmount(), $order),
            $this->formatPrice($item->getDiscountAmount(), $order),
            $this->formatPrice($this->getItemTotal($item), $order),
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
            return 'Empty';
        }
    }
}
?>