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
class Neo_Adminhtml_Block_Report_Custom_Sales_Export_Ecomexpressshipmentscod extends Mage_Adminhtml_Block_Widget_Grid
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
		/*$collection->getSelect()
                ->joinLeft(array('invoice' => 'sales_flat_invoice'), 
			"invoice.entity_id = main_table.invoice_id", 
			array('invoice_increment_id' => 'invoice.increment_id'));*/

		$collection->getSelect()
                ->joinLeft(array('order' => 'sales_flat_order'), 
			"order.entity_id = main_table.order_id", 
			array('grand_total'=>'order.cod_fee','coll_val' => 'order.grand_total','order_increment_id' => 'order.increment_id','customer_firstname' => 'order.customer_firstname','customer_lastname' => 'order.customer_lastname'));
			
		$collection->getSelect()
                ->joinLeft(array('order_shippingaddress' => 'sales_flat_order_address'), 
			"order_shippingaddress.parent_id = main_table.order_id AND order_shippingaddress.address_type = 'shipping'", 
			array('firstname' => 'order_shippingaddress.firstname','lastname' => 'order_shippingaddress.lastname','street' => 'order_shippingaddress.street','city' => 'order_shippingaddress.city','region' => 'order_shippingaddress.region','postcode' => 'order_shippingaddress.postcode','country_id' => 'order_shippingaddress.country_id','telephone' => 'order_shippingaddress.telephone'));
			
		$collection->getSelect()
                ->join(array('shipment_track' => 'sales_flat_shipment_track'), 
			"shipment_track.parent_id = main_table.entity_id", 
			array('track_number' => 'shipment_track.track_number','track_title' => 'shipment_track.title'/*,'pod_number' => 'shipment_track.pod_number','pod_date' => 'shipment_track.pod_date'*/));
			
		$collection->getSelect()
                ->join(array('shipment_item' => 'sales_flat_shipment_item'), 
			"shipment_item.parent_id = main_table.entity_id",
			array('shipment_weight' => 'SUM(shipment_item.weight)','item_sku' => 'GROUP_CONCAT(shipment_item.sku)'));

		$collection->getSelect()
                ->where("main_table.created_at >= ?", $fromdate." 00:00:00")
                ->where("main_table.created_at <= ?", $todate." 23:59:59");
				
		$collection->getSelect()->group('main_table.entity_id');			
		$collection->getSelect()->order('shipment_track.title');			
		//echo $collection->getSelect();
		//exit;
		$this->setCollection($collection);
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
				'header'    => Mage::helper('reports')->__('Air Waybill number'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'track_number',
		));	
		
		$this->addColumn('order_increment_id', array(
				'header'    => Mage::helper('reports')->__('Order Number'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'order_increment_id',
		));	
		
		$this->addColumn('Product', array(
				'header'    => Mage::helper('reports')->__('Product'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'Product',
				'default'   => 'COD',
		));	
		
		$this->addColumn('Shipper', array(
				'header'    => Mage::helper('reports')->__('Shipper'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'Shipper',
				'default'     => 'GNG ELECTRONICS PVT LTD',
		));
		
		$this->addColumn('firstname_storname', array(
				'header'    => Mage::helper('reports')->__('Consignee'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'firstname',
		));	
		
		$this->addColumn('street', array(
				'header'    => Mage::helper('reports')->__('Consignee Address1'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'street',
		));	
		$this->addColumn('consignee_city', array(
				'header'    => Mage::helper('reports')->__('Consignee Address2'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'city',
		));	
		$this->addColumn('consignee_region', array(
				'header'    => Mage::helper('reports')->__('Consignee Address3'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'region',
		));	
		$this->addColumn('city', array(
				'header'    => Mage::helper('reports')->__('Destination City'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'city',
		));
		$this->addColumn('postcode', array(
				'header'    => Mage::helper('reports')->__('Pincode'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'postcode',
		));	
		$this->addColumn('region', array(
				'header'    => Mage::helper('reports')->__('State'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'region',
		));
		$this->addColumn('telephoneno', array(
				'header'    => Mage::helper('reports')->__('Mobile'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'telephone',
		));	
		$this->addColumn('telephone', array(
				'header'    => Mage::helper('reports')->__('Telephone'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'telephone',
		));			
							
		$this->addColumn('item_sku', array(
				'header'    => Mage::helper('reports')->__('Item Description'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'item_sku',
		));	
		
		$this->addColumn('Pieces', array(
				'header'    => Mage::helper('reports')->__('Pieces'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'Pieces',
				'default'     => '',
		));	
		
		$this->addColumn('coll_val', array(
				'header'    => Mage::helper('reports')->__('Collectable Value'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'coll_val',
		));	
		
		$this->addColumn('grand_total', array(
				'header'    => Mage::helper('reports')->__('Declared value'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'grand_total',
		));
		
		$this->addColumn('shipment_weight', array(
				'header'    => Mage::helper('reports')->__('Actual Weight'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'shipment_weight',
		));	
		
		$this->addColumn('shipcode', array(
				'header'    => Mage::helper('reports')->__('SHIPPER CODE'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'shipcode',
				'default'     => '49172',
		));
		
		return parent::_prepareColumns();
	}	
}
