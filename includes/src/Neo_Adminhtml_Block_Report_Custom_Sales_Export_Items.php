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
class Neo_Adminhtml_Block_Report_Custom_Sales_Export_Items extends Mage_Adminhtml_Block_Widget_Grid
{	
   public function __construct()
	{
	  parent::__construct();
	  $this->setId('exportOrderItemGrid');
	  $this->setDefaultSort('item_id');
	  $this->setDefaultDir('ASC');
	  $this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{		
		$fromdate = $this->getRequest()->getPost('from_date');
		$todate = $this->getRequest()->getPost('to_date');

		$productTypes = array('simple', 'virtual');
		$collection = Mage::getResourceModel('sales/order_item_collection')
				->addFieldToFilter('product_type', array('in' => $productTypes));			
		$collection->getSelect()
                ->joinLeft(array('order' => 'sales_flat_order'), 
			"order.entity_id = main_table.order_id", 
			array('order_increment_id' => 'order.increment_id'));
		$collection->getSelect()
                ->where("main_table.created_at >= ?", $fromdate." 00:00:00")
                ->where("main_table.created_at <= ?", $todate." 23:59:59");
		//$collection->printLogQuery(true);die;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}


  	/**
     * Prepare grid columns
     *
     * @return Neo_Adminhtml_Block_Report_Custom_Sales_Export_Items
     */
  	protected function _prepareColumns()
	{
		$this->addColumn('name', array(
			'header'    => Mage::helper('reports')->__('Item Name'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'name',
		));

		$this->addColumn('sku', array(
			'header'    => Mage::helper('reports')->__('Sku'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'sku',
		));

		$this->addColumn('created_at', array(
            'header'    => Mage::helper('reports')->__('Ordered Date'),
            'index'     => 'created_at',
            'type'      => 'date',
			'filter'	=> false,
            'sortable'  => false
        ));

		$this->addColumn('order_increment_id', array(
			'header'    => Mage::helper('reports')->__('Order Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'order_increment_id',
		));
		
		$this->addColumn('created_at_time', array(
            'header'    => Mage::helper('reports')->__('Order Time'),
            'index'     => 'created_at',
            'renderer'  => 'Neo_adminhtml/report_custom_sales_export_column_renderer_time',
			'filter'	=> false,
            'sortable'  => false
        ));		

		$currencyCode = $this->getCurrentCurrencyCode();

		$this->addColumn('price', array(
			'header'    => Mage::helper('reports')->__('Price'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'price',
            'type'		=> 'currency',
            'currency_code'  => $currencyCode,
		));

		$this->addColumn('row_total_incl_tax', array(
			'header'    => Mage::helper('reports')->__('Row Total'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'row_total_incl_tax',
            'type'		=> 'currency',
            'currency_code'  => $currencyCode,
		));

		$this->addColumn('tax_amount', array(
			'header'    => Mage::helper('reports')->__('Tax Amount'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'tax_amount',
            'type'		=> 'currency',
            'currency_code'  => $currencyCode,
		));

		$this->addColumn('qty_ordered', array(
			'header'    => Mage::helper('reports')->__('Ordered Qty'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'qty_ordered',
		));

		$this->addColumn('qty_invoiced', array(
			'header'    => Mage::helper('reports')->__('Invoice Qty'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'qty_invoiced',
		));

		$this->addColumn('qty_canceled', array(
			'header'    => Mage::helper('reports')->__('Cancelled Qty'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'qty_canceled',
		));

		$this->addColumn('qty_shipped', array(
			'header'    => Mage::helper('reports')->__('Ship Qty'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'qty_shipped',
		));

		$this->addColumn('qty_refunded', array(
			'header'    => Mage::helper('reports')->__('Refunded Qty'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'qty_refunded',
		));
		$this->addColumn('coupon_code', array(
			'header'    => Mage::helper('reports')->__('Discount Codes'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'coupon_code',
		));
		$this->addColumn('discount_amount', array(
			'header'    => Mage::helper('reports')->__('Discount Amount'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'discount_amount',
		));

		return parent::_prepareColumns();
	}		

    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? Mage::app()->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }
}
