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
class Neo_Adminhtml_Block_Report_Custom_Sales_Export_Shipment extends Mage_Adminhtml_Block_Widget_Grid
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
			array('order_increment_id' => 'order.increment_id'));

		$collection->getSelect()
                ->joinLeft(array('shipment_track' => 'sales_flat_shipment_track'), 
			"shipment_track.parent_id = main_table.entity_id", 
			array('track_number' => 'shipment_track.track_number','track_title' => 'shipment_track.title','pod_number' => 'shipment_track.pod_number','pod_date' => 'shipment_track.pod_date'));

		$collection->getSelect()
                ->where("main_table.created_at >= ?", $fromdate." 00:00:00")
                ->where("main_table.created_at <= ?", $todate." 23:59:59");
		$collection->getSelect()->group('main_table.entity_id');
		//$collection->printLogQuery(true);die;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}


  	/**
     * Prepare grid columns
     *
     * @return Neo_Adminhtml_Block_Report_Custom_Sales_Export_Shipment
     */
  	protected function _prepareColumns()
	{
		$this->addColumn('increment_id', array(
			'header'    => Mage::helper('reports')->__('Shipment Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'increment_id',
		));

		$this->addColumn('created_at', array(
            'header'    => Mage::helper('reports')->__('Shipment Date'),
            'index'     => 'created_at',
            'type'      => 'date',
			'filter'	=> false,
            'sortable'  => false
        ));
		
		$this->addColumn('created_at_time', array(
            'header'    => Mage::helper('reports')->__('Shipment Time'),
            'index'     => 'created_at',
            'renderer'  => 'Neo_adminhtml/report_custom_sales_export_column_renderer_time',
			'filter'	=> false,
            'sortable'  => false
        ));

		$this->addColumn('order_increment_id', array(
			'header'    => Mage::helper('reports')->__('Order Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'order_increment_id',
		));

		$this->addColumn('invoice_increment_id', array(
			'header'    => Mage::helper('reports')->__('Invoice Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'invoice_increment_id',
		));

		$this->addColumn('total_qty', array(
			'header'    => Mage::helper('reports')->__('Shipment Qty'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'total_qty',
		));

		$this->addColumn('track_number', array(
			'header'    => Mage::helper('reports')->__('Tracking Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'track_number',
		));

		$this->addColumn('track_title', array(
			'header'    => Mage::helper('reports')->__('Tracking Title'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'track_title',
		));

		$this->addColumn('pod_number', array(
			'header'    => Mage::helper('reports')->__('Pod Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'pod_number',
		));

		$this->addColumn('pod_date', array(
			'header'    => Mage::helper('reports')->__('Pod Date'),
			'align'     =>'left',
			'width'     => '50px',
            'type'      => 'date',
			'index'     => 'pod_date',
		));

	  return parent::_prepareColumns();
	}	
}
