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
 * Adminhtml sales stock report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Neo_Adminhtml_Block_Report_Custom_Sales_Export_Bluedartshipments extends Mage_Adminhtml_Block_Widget_Grid
{	
   public function __construct()
	{
	  parent::__construct();
	  $this->setId('exportShipmentGrid');
	  $this->setDefaultSort('entity_id');
	  $this->setDefaultDir('ASC');
	  $this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{		
		$fromdate = $this->getRequest()->getPost('from_date');
		$todate = $this->getRequest()->getPost('to_date');
		

		$collection = Mage::getResourceModel('sales/order_shipment_collection');
		$collection->getSelect()
                ->joinLeft(array('invoice' => 'sales_flat_invoice'), 
			"invoice.entity_id = main_table.invoice_id", 
			array('invoice_increment_id' => 'invoice.increment_id'));

		$collection->getSelect()
                ->joinLeft(array('order' => 'sales_flat_order'), 
			"order.entity_id = main_table.order_id", 
			array('grand_total','order_increment_id' => 'order.increment_id','customer_firstname' => 'order.customer_firstname','customer_lastname' => 'order.customer_lastname'));
			
		$collection->getSelect()
                ->joinLeft(array('order_shippingaddress' => 'sales_flat_order_address'), 
			"order_shippingaddress.parent_id = main_table.order_id AND order_shippingaddress.address_type = 'shipping'", 
			array('firstname' => 'order_shippingaddress.firstname','lastname' => 'order_shippingaddress.lastname','street' => 'order_shippingaddress.street','city' => 'order_shippingaddress.city','region' => 'order_shippingaddress.region','postcode' => 'order_shippingaddress.postcode','country_id' => 'order_shippingaddress.country_id','telephone' => 'order_shippingaddress.telephone'));
			
		$collection->getSelect()
                ->join(array('shipment_track' => 'sales_flat_shipment_track'), 
			"shipment_track.parent_id = main_table.entity_id AND shipment_track.carrier_code IN ('bluedart','bluedart_apex','bluedart_sfc','bluedart_surface','bluedart_dpcourier')", 
			array('track_number' => 'shipment_track.track_number','track_title' => 'shipment_track.title','pod_number' => 'shipment_track.pod_number','pod_date' => 'shipment_track.pod_date'));
			
		$collection->getSelect()
                ->join(array('shipment_item' => 'sales_flat_shipment_item'), 
			"shipment_item.parent_id = main_table.entity_id", 
			array('shipment_weight' => 'SUM(shipment_item.weight)'));

		$collection->getSelect()
                ->where("main_table.created_at >= ?", $fromdate." 00:00:00")
                ->where("main_table.created_at <= ?", $todate." 23:59:59");
				
		$collection->getSelect()->group('main_table.entity_id');		
		$this->setCollection($collection);
		//$this->setExtraCollection($collection);
		return parent::_prepareCollection();
	}
	
	public function setExtraCollection($collection){
		$orderIds = array();
		foreach($collection as $shipment){
			$orderIds[] = $shipment->getOrderId();
		}
		$shippingAddressCollection = Mage::getModel('sales/order_address')->getCollection()
							->addFieldToFilter('parent_id' , array('in' => $orderIds))
							->addFieldToFilter('address_type' , 'shipping');
		$shippingaddresses = array();
		foreach($shippingAddressCollection as $address){
			$shippingaddresses['order_id'][] = $address->getParentId();			
			$shippingaddresses[$address->getParentId()]['address'] = $address->getFirstname().' '.$address->getLastname().', '.$address->getStreetFull().', '.$address->getCity().', '.$address->getRegion().' - '.$address->getPostcode().', '.$address->getCountryId().', '.$address->getTelephone();
		}
		
		Mage::register('shipping_addresses', $shippingaddresses);
	}

  	/**
     * Prepare grid columns
     *
     * @return Neo_Adminhtml_Block_Report_Custom_Sales_Export_Shipment
     */
  	protected function _prepareColumns()
	{
		$this->addColumn('track_number', array(
				'header'    => Mage::helper('reports')->__('Awbno'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'track_number',
			));	
		
		$this->addColumn('order_increment_id', array(
			'header'    => Mage::helper('reports')->__('OrderNo'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'order_increment_id',
		));
		
		$this->addColumn('company', array(
			'header'    => Mage::helper('reports')->__('Company'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'company ',
		));
		
		/*$this->addColumn('invoice_increment_id', array(
			'header'    => Mage::helper('reports')->__('Invoice Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'invoice_increment_id',
		));

		$this->addColumn('increment_id', array(
			'header'    => Mage::helper('reports')->__('Shipment Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'increment_id',
		));*/
		
		$this->addColumn('customer_name', array(
			'header'    => Mage::helper('reports')->__('Name'),
			'align'     =>'left',
			'width'     => '50px',
			'renderer'  => 'neo_adminhtml/report_custom_sales_export_column_renderer_name',
			'index'     => 'customer_name',
		));
		
		/*$this->addColumn('billing_address', array(
			'header'    => Mage::helper('reports')->__('Billing Address'),
			'align'     =>'left',
			'width'     => '50px',
			'renderer'  => 'neo_adminhtml/report_custom_sales_export_column_renderer_billingaddress',
			'index'     => 'billing_address',
		));*/
		
		
		$this->addColumn('shipping_address', array(
			'header'    => Mage::helper('reports')->__('Address1'),
			'align'     =>'left',
			'width'     => '50px',
			'renderer'  => 'neo_adminhtml/report_custom_sales_export_column_renderer_shippingaddress',
			'index'     => 'shipping_address',
		));
		$this->addColumn('Address2', array(
			'header'    => Mage::helper('reports')->__('Address2'),
			'align'     =>'left',
			'width'     => '50px',
			/*'renderer'  => 'neo_adminhtml/report_custom_sales_export_column_renderer_shippingaddress',*/
			'index'     => 'Address2',
		));
		$this->addColumn('Address3', array(
			'header'    => Mage::helper('reports')->__('Address3'),
			'align'     =>'left',
			'width'     => '50px',
			/*'renderer'  => 'neo_adminhtml/report_custom_sales_export_column_renderer_shippingaddress',*/
			'index'     => 'Address3',
		));
		$this->addColumn('postcode ', array(
			'header'    => Mage::helper('reports')->__('Pincode'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'postcode',
		));
		$this->addColumn('telephone ', array(
			'header'    => Mage::helper('reports')->__('Phone'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'telephone',
		));
		$this->addColumn(' ', array(
			'header'    => Mage::helper('reports')->__('Mobile'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => ' ',
		));
		$this->addColumn('shipment_weight', array(
			'header'    => Mage::helper('reports')->__('Weight'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'shipment_weight',
		));		
		
		$this->addColumn('grand_total', array(
			'header'    => Mage::helper('reports')->__('Amount'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'grand_total',            
		));
		
		
		
		$this->addColumn('shipment_qty', array(
			'header'    => Mage::helper('reports')->__('Quantity'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'shipment_qty',
		));
		
		$this->addColumn('shipment_cmdty', array(
			'header'    => Mage::helper('reports')->__('Cmdty'),
			'index'     => 'shipment_cmdty',
			'default'   => '014'
		));
		
		return parent::_prepareColumns();
	}	
}
