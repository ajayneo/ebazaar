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

class Neo_Reports_Model_Mysql4_Sales_Order_Invoice_Collection extends Mage_Sales_Model_Mysql4_Order_Invoice_Collection
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
     * Add Customer Address Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Invoice_Collection
     */
    public function addCustomerAddressDetails(){		
        $orderAddressTable = $this->getTable('sales/order_address');
        $this->getSelect()
                ->join(array('order_address_table' => $orderAddressTable), "order_address_table.parent_id = main_table.order_id AND order_address_table.address_type = 'billing'", array('firstname' => 'order_address_table.firstname', 'lastname' => 'order_address_table.lastname', 'city' => 'order_address_table.city'));
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
                ->joinLeft(array('shipment_table' => $shipmentTable), "shipment_table.invoice_id = main_table.entity_id", array('shipment_date' => 'shipment_table.created_at'))
			
            ->joinLeft(array('shipment_track_table' => $shipmentTrackTable), "shipment_track_table.parent_id = shipment_table.entity_id", array('shipment_track_title' => 'shipment_track_table.title', 'shipment_track_number' => 'shipment_track_table.track_number', 'delivered_date' => 'shipment_track_table.pod_date'));
	  
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
                ->joinLeft(array('order_address' => $orderAddressTable), "order_address.parent_id = main_table.order_id AND order_address.address_type ='shipping'", array('shipping_city' => 'order_address.city','shipping_region' => 'order_address.region', 'shipping_zip' => 'order_address.postcode'));
        return $this;
    }
	/**
     * Add Order Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Invoice_Collection
     */
    public function addOrderFieldsToSelect(){		
        $orderTable = $this->getTable('sales/order');
        $this->getSelect()
							->joinLeft(
							array('order_table' => $orderTable), 
							"order_table.entity_id = main_table.order_id", 
							array(	'order_id' => 'order_table.entity_id',
									'order_increment_id' => 'order_table.increment_id', 
									'order_created_at' => 'order_table.created_at',
									'order_updated_at' => 'order_table.updated_at',
									'order_status' => 'order_table.status',
									'customer_firstname' => 'order_table.customer_firstname',
									'customer_lastname' => 'order_table.customer_lastname',
									'rep_code' => 'order_table.rep_code',
									'paytype1' => 'order_table.paytype1',
									'totalamt1' => 'order_table.totalamt1',
									'paytype2' => 'order_table.paytype2',
									'totalamt2' => 'order_table.totalamt2',
									'total_qty_ordered' => 'order_table.total_qty_ordered',
									'order_discount_amount' => 'order_table.discount_amount',
									'coupon_code' => 'order_table.coupon_code',
								)
							);
        return $this;
	}
}
