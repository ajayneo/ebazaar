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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * Flat sales order collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Neo_Reports_Model_Mysql4_Sales_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection
{	
    /**
     * Initialize initial fields to select like id field
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _initInitialFieldsToSelect() {
        return $this;
    }

    /**
     * Add store ids to filter
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Mysql4_Quote_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFieldToFilter('main_table.store_id', array('in' => $storeIds));
        return $this;
    }
	
	/**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        
        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

	/**
     * Add payment method
     *
     * @return Atlantum_Reports_Model_Mysql4_Sales_Order_Creditmemo_Collection
     */
    public function addPaymentMethodDetails() {
        $paymentTable = $this->getTable('sales/order_payment');
        $coreConfig = $this->getTable('core/config_data');
        $this->getSelect()
                ->join(array('payment_table' => $paymentTable), "payment_table.parent_id = main_table.entity_id", array(
                    'payment_method' => 'method',
                ))
                ->join(array('payment_method' => $coreConfig), "payment_method.path = CONCAT('payment/', payment_table.method, '/title') and payment_method.scope='default'", array(
                    'payment_title' => 'value',
                ))
        ;
        return $this;
    }

	/**
     * Add Shipping Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Collection
     */
    public function addShippingDetails() {
        $orderAddressTable = $this->getTable('sales/order_address');
        $this->getSelect()
                ->joinLeft(array('order_address' => $orderAddressTable), "order_address.parent_id = main_table.entity_id AND order_address.address_type ='shipping'", array('shipping_city' => 'order_address.city','shipping_region' => 'order_address.region', 'shipping_zip' => 'order_address.postcode', 'shipping_street' => 'order_address.street'));
        return $this;
    }
	/**
     * Add Invoice Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Collection
     */
    public function addInvoiceFieldsToSelect(){		
        $invoiceTable = $this->getTable('sales/invoice');
        $this->getSelect()
                ->joinLeft(array('invoice_table' => $invoiceTable), "invoice_table.order_id = main_table.entity_id", array('order_id' => 'invoice_table.order_id','invoice_id' => 'invoice_table.increment_id', 'invoice_date' => 'invoice_table.created_at', 'invoice_amount' => 'invoice_table.grand_total', 'invoice_discount_amount'=> 'invoice_table.discount_amount', 'invoice_cod_fee'=> 'invoice_table.cod_fee', 'invoice_is_cod'=> 'invoice_table.is_cod'));
        return $this;
	}

	/**
     * Add Shipment Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Collection
     */
    public function addShipmentFieldsToSelect(){		
        $shipmentTable = $this->getTable('sales/shipment');
		$shipmentTrackTable = $this->getTable('sales/shipment_track');
        $this->getSelect()
                ->joinLeft(array('shipment_table' => $shipmentTable), "shipment_table.order_id = main_table.entity_id", array('shipment_date' => 'shipment_table.created_at'))
			
                ->joinLeft(array('shipment_track_table' => $shipmentTrackTable), "shipment_track_table.order_id = main_table.entity_id", array('shipment_track_title' => 'shipment_track_table.title', 'shipment_track_number' => 'shipment_track_table.track_number', 'delivered_date' => 'shipment_track_table.pod_date'));
        return $this;
	}
	
	/**
     * Add Billing Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Collection
     */
    public function addBillingDetails() {
        $orderAddressTable1 = $this->getTable('sales/order_address');
        $this->getSelect()
                ->joinLeft(array('order_address1' => $orderAddressTable1), "order_address1.parent_id = main_table.entity_id AND order_address1.address_type ='billing'", array('billing_city' => 'order_address1.city','billing_region' => 'order_address1.region', 'billing_zip' => 'order_address1.postcode', 'billing_street' => 'order_address1.street', 'billing_email' => 'order_address1.email', 'billing_telephone' => 'order_address1.telephone', 'billing_countryid'=>'order_address1.country_id'))
				->joinLeft(array('customer_confirmed' => 'customer_entity_varchar'), "customer_confirmed.entity_id = main_table.customer_id AND customer_confirmed.attribute_id=16", array('confirmed_value'=>'customer_confirmed.value'))
				->joinLeft(array('customer_create' => 'customer_entity'), "customer_create.entity_id = main_table.customer_id", array('customer_since'=>'customer_create.created_at'))
				->joinLeft(array('customer_logvisit' => 'log_customer'), "customer_logvisit.log_id = (select max(log_id) from log_customer as c1 where c1.customer_id=main_table.customer_id group by c1.customer_id)", array('last_logged'=>'customer_logvisit.logout_at'));
        return $this;
    }
	
	

}
