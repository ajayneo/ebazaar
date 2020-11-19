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
class Neo_Adminhtml_Block_Report_Custom_Sales_Export_Creditmemo extends Mage_Adminhtml_Block_Widget_Grid
{	
   public function __construct()
	{
	  parent::__construct();
	  $this->setId('exportCreditmemoGrid');
	  $this->setDefaultSort('entity_id');
	  $this->setDefaultDir('ASC');
	  $this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{		
		$fromdate = $this->getRequest()->getPost('from_date');
		$todate = $this->getRequest()->getPost('to_date');

		$collection = Mage::getResourceModel('sales/order_creditmemo_collection');
		$collection->getSelect()
                ->joinLeft(array('invoice' => 'sales_flat_invoice'), 
			"invoice.entity_id = main_table.invoice_id", 
			array('invoice_increment_id' => 'invoice.increment_id'));

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
     * @return Neo_Adminhtml_Block_Report_Custom_Sales_Export_Shipment
     */
  	protected function _prepareColumns()
	{
		$this->addColumn('increment_id', array(
			'header'    => Mage::helper('reports')->__('Creditmemo Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'increment_id',
		));

		$this->addColumn('created_at', array(
            'header'    => Mage::helper('reports')->__('Creditmemo Date'),
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

		$this->addColumn('invoice_increment_id', array(
			'header'    => Mage::helper('reports')->__('Invoice Number'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'invoice_increment_id',
		));
		
		$this->addColumn('created_at_time', array(
            'header'    => Mage::helper('reports')->__('Creditmemo Time'),
            'index'     => 'created_at',
            'renderer'  => 'Neo_adminhtml/report_custom_sales_export_column_renderer_time',
			'filter'	=> false,
            'sortable'  => false
        ));

		$currencyCode = $this->getCurrentCurrencyCode();

		$this->addColumn('grand_total', array(
			'header'    => Mage::helper('reports')->__('Creditmemo Amount'),
			'align'     =>'left',
			'width'     => '50px',
			'index'     => 'grand_total',
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
