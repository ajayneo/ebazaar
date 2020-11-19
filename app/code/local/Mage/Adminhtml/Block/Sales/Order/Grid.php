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
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        /*$collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();*/
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->join(array('order'=>'sales/order'),'main_table.increment_id=order.increment_id','mapped_status');
        $collection->join(array('payment'=>'sales/order_payment'),'main_table.entity_id=parent_id','method');
       // $collection->join(array('customer'=>'customer'),'main_table.customer_id=entity_id','email');
        $this->setCollection($collection);
        /*echo "<pre>";
        foreach ($collection as $order) {
            # code...
            print_r($order->getData());
            die;
        }*/
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index'=>'main_table.increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index' => 'main_table.created_at',
        ));

         $this->addColumn('updated_at', array(
            'header' => Mage::helper('sales')->__('Updated On'),
            'index' => 'updated_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index' => 'main_table.updated_at',
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('sales')->__('Customer Email'),
            'index' => 'customer_id',
            'filter_name' => 'customer_email',
            'renderer' => 'Mage_Adminhtml_Block_Sales_Order_Customerdata',
            'filter_condition_callback' => array($this, '_filterHasCustomerConditionCallback')
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));



        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('Grand Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'filter_index'=>'main_table.grand_total',
        ));

        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode=>$paymentModel){
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = $paymentTitle;
        }

        $this->addColumn('method', array(
            'header' => Mage::helper('sales')->__('Payment Method'),
            'index' => 'method',
            'filter_index' => 'payment.method',
            'type'  => 'options',
            'width' => '70px',
            'options' => $methods,
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'filter_index'=>'main_table.status',
        ));

        $this->addColumn('cust_map_status', array(
            'header' => Mage::helper('sales')->__('Customer<br>Map'),
            'index' => 'customer_id',
            'type'  => 'text',
            'width' => '70px',
            'filter' => false,
            'filter_name' => 'cust_map_status',
            'renderer' => 'Mage_Adminhtml_Block_Sales_Order_Customerdata'
        ));

        $this->addColumn('mapped_status',
            array(
                'header'=> Mage::helper('catalog')->__('Nav<br>Mapped<br>Status'),
                'width' => '70px',
                'index' => 'mapped_status',
                'type'  => 'options',
                'options' => array('1'=>'Yes','0'=>'No'),
        ));

        //code for ARM assisting added by on 10th Apr 2018
        $this->addColumn('assisted_by_arm',
            array(
                'header'=> Mage::helper('catalog')->__('Assisted By<br>Arm'),
                'width' => '70px',
                'index' => 'assisted_by_arm',
                'type'  => 'options',
                'options' => array('1'=>'Yes','0'=>'No'),
                'renderer' => 'Mage_Adminhtml_Block_Sales_Order_Assistedbyarm'
        ));
        //end code for ARM assisting

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }


    protected function _filterHasCustomerConditionCallback($collection, $column)
    {
        
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $customer = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email', array('like' => '%'.$value.'%'));
        
        $customer_ids = $customer->getColumnValues('entity_id');
        $this->getCollection()->addFieldToFilter('customer_id',array('in'=>$customer_ids));
        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        $modeOptions = $this->getStockLocationOptions();

        $this->getMassactionBlock()->addItem('change_mode', array(
            'label'         => Mage::helper('index')->__('Change Stock Location'),
            'url'           => $this->getUrl('*/*/massChangeStockLocation'),
            'additional'    => array(
                'mode'      => array(
                    'name'      => 'index_mode',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('index')->__('Stock Location'),
                    'values'    => $modeOptions
                )
            )
        ));

        //$this->getMassactionBlock()->addItem('process_order_for_shipments', array(
        //     'label'=> Mage::helper('sales')->__('Process orders For Shipments'),
        //     'url'  => $this->getUrl('*/sales_order/processOrderForShipments'),
        //));

        $this->getMassactionBlock()->addItem('process_order_in_navision', array(
             'label'=> Mage::helper('sales')->__('Process orders In Navision'),
             'url'  => $this->getUrl('*/sales_order/processOrderInNavision'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function getStockLocationOptions()
    { 
        return Mage::helper('stocklocation')->getStockLocationOptions();
    }   

}
