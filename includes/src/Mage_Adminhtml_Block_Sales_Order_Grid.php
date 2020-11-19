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
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id',array('method'));
        $collection->getSelect()->joinLeft('sales_order_stock_location', 'main_table.entity_id = sales_order_stock_location.order_id',array('stock_location'));
        $collection->getSelect()->joinLeft('sales_flat_order_address','main_table.entity_id = sales_flat_order_address.parent_id AND sales_flat_order_address.address_type="shipping"',array('sales_flat_order_address.region'));
        
        //Start by Pradeep Sanku on 06April2015 for filtering by customer group.
        $collection->getSelect()->join(
          array('oe'=>'sales_flat_order'),
          'oe.entity_id=main_table.entity_id',
          array('oe.customer_group_id')
        );
        //End by Pradeep Sanku on 06April2015 for filtering by customer group.

        //Start by Shailendra Gupta on 14oct2014 for filtering the records as per the user login.
        $adminUser = Mage::getSingleton('admin/session')->getUser()->getRole()->getData();        
        if ($adminUser['role_id'] == 4) { //role id 4 is warehouse
            $collection->addAttributeToFilter('status', array('in' => array('financeapproved','codverified','processing','codpaymentpending','complete')));
        }else if ($adminUser['role_id'] == 11) { //role id 11 is finance
            $collection->addAttributeToFilter('status', array('in' => array('codpaymentpending','pendingbilldesk','pendingcod')));
        }
        //End by Shailendra Gupta on 14oct2014 for filtering the records as per the user login.
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // add by pradeep sanku on 6th april for the customer group column in the sales order grid
        $groups = Mage::getResourceModel('customer/group_collection')->load()->toOptionHash();
        // add by pradeep sanku on 6th april for the customer group column in the sales order grid

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
                'filter_index'=>'main_table.store_id',
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index'=>'main_table.created_at',
        ));

        // add by pradeep sanku on 6th april for the customer group column in the sales order grid
        $this->addColumn('customer_group',array(
            'header'=> Mage::helper('sales')->__('Customer Group'),
            'width' => '70px',
            'index' => 'customer_group_id',
            'type' => 'options',
            'options' => $groups,
        ));
        // add by pradeep sanku on 6th april for the customer group column in the sales order grid

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'filter_index'=>'main_table.billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
            'filter_index'=>'main_table.shipping_name',
        ));
        
        $this->addColumn('region', array(
            'header' => Mage::helper('sales')->__('Region'),
            'width' => '175px',
            'index' => 'region',
            //'sortable'  =>false,
            //'filter' => false, 
            'filter_index' => 'sales_flat_order_address.region',
        ));
        
        $optionsArrayPayment = array('banktransfer' => 'Bank Transfer Payment','cashondelivery' => 'Cash On Delivery ','neftrtgs' => 'NEFT/RTGS', 'innoviti' => 'EMI through Credit Card', 'billdesk_ccdc_net' => 'Credit/Debit Card or Netbanking ', 'hdfc_cdl' => 'HDFC Bank','free'=>'Free');
        
        $this->addColumn('method', array(
            'header' => Mage::helper('sales')->__('Payment Method'),
            'index' => 'method',
            'type'  => 'options',
            'width' => '200px',
            'filter' => false,  
            'options' => $optionsArrayPayment,
        ));
        
        $this->addColumn('protect_sku', array(
            'header' => Mage::helper('sales')->__('Product SKU'),           
            'index' => 'protect_sku',
            'type'  => 'text',      
            'width' => '200px',
            'sortable'  =>false,
            'filter' => false,                                         
            'renderer' => 'Neo_StockLocation_Block_Adminhtml_Renderer_Productssku',          
        ));

        $this->addColumn('agent', array(
            'header' => Mage::helper('sales')->__('User Agent'),           
            'index' => 'agent',
            'type'  => 'text',      
            'width' => '200px',
            'sortable'  =>false,
            'filter' => false,                                         
            'renderer' => 'Neo_StockLocation_Block_Adminhtml_Renderer_Mobile',          
        ));

        $optionsArrayLocation = $this->getStockLocationOptions();
        
        $this->addColumn('stock_location', array(
            'header' => Mage::helper('sales')->__('Warehouse Location'),
            'width' => '175px',
            'index' => 'stock_location',
            'type'  => 'options',
            //'filter' => false, 
            'options' => $optionsArrayLocation,
            'filter_index' => 'sales_order_stock_location.stock_location',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'filter_index'=>'main_table.base_grand_total',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
            'filter_index'=>'main_table.grand_total',
        ));

        //Start by Shailendra Gupta on 14oct2014 for filtering the records as per the user login.
        $adminUser = Mage::getSingleton('admin/session')->getUser()->getRole()->getData();
        if ($adminUser['role_id'] == 4) {
            $optionsArray = array('codverified' => 'COD Verified', 'financeapproved' => 'Finance Approved', 'processing' => 'Processing','codpaymentpending' => 'COD Payment Pending', 'complete' => 'Complete');
        }else if ($adminUser['role_id'] == 11) {
            $optionsArray = array('pendingcod' => 'COD Verification Pending', 'codpaymentpending' => 'COD Payment Pending', 'pendingbilldesk' => 'Finance Approval Pending');
        }else{
            $optionsArray = Mage::getSingleton('sales/order_config')->getStatuses(); 
        }
        //End by Shailendra Gupta on 14oct2014 for filtering the records as per the user login.

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            //'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            'options' => $optionsArray,
            'filter_index'=>'main_table.status',
        ));

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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);

        $this->getMassactionBlock()->addItem('delete_order_shipment', array(
            'label'=> Mage::helper('sales')->__('Shipment Delete'),
            'url'  => $this->getUrl('*/sales_order/shipmentDelete'),
            'confirm' => Mage::helper('sales')->__('Are you sure to delete the shipment ?'),
        ));
        
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

        $adminUser = Mage::getSingleton('admin/session')->getUser()->getRole()->getData();
        if ($adminUser['role_id'] != 4 && $adminUser['role_id'] != 11) {
            
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

        }
        
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
