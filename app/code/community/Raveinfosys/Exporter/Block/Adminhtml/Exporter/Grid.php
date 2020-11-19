<?php

class Raveinfosys_Exporter_Block_Adminhtml_Exporter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_export_grid');
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
        //$collection->getSelect()->joinLeft(array('myorder'=>'sales_flat_order'),'myorder.entity_id = main_table.entity_id',array('myorder.customer_email'));
        //$collection->getSelect()->joinLeft('sales_order_stock_location', 'main_table.entity_id = sales_order_stock_location.order_id',array('stock_location'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        /*$this->addColumn('customer_email', array(
            'header' => $this->helper('sales')->__('Customer Email'),
            'index' => 'customer_email',
            'filter_index' => 'myorder.customer_email',
        ));*/

        /*$optionsArrayLocation = $this->getStockLocationOptions();
        
        $this->addColumn('stock_location', array(
            'header' => Mage::helper('sales')->__('Warehouse Location'),
            'width' => '175px',
            'index' => 'stock_location',
            'type'  => 'options',
            //'filter' => false, 
            'options' => $optionsArrayLocation,
            'filter_index' => 'sales_order_stock_location.stock_location',
        ));*/

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type' => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action', array(
                'header' => Mage::helper('sales')->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url' => array('base' => 'adminhtml/sales_order/view'),
                        'field' => 'order_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        }


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);

        $this->getMassactionBlock()->addItem('export_order', array(
            'label' => Mage::helper('sales')->__('Export Orders'),
            'url' => $this->getUrl('*/*/exportCsv'),
        ));

        //$modeOptions = $this->getStockLocationOptions();

        //$this->getMassactionBlock()->addItem('change_mode', array(
        //    'label'         => Mage::helper('index')->__('Change Stock Location'),
        //    'url'           => $this->getUrl('*/*/massChangeStockLocation'),
        //    'additional'    => array(
        //        'mode'      => array(
        //            'name'      => 'index_mode',
        //            'type'      => 'select',
        //            'class'     => 'required-entry',
        //            'label'     => Mage::helper('index')->__('Stock Location'),
        //            'values'    => $modeOptions
        //        )
        //    )
        //));


        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return false;
    }

    protected function getStockLocationOptions()
    {
        return Mage::helper('stocklocation')->getStockLocationOptions();
    }

}
