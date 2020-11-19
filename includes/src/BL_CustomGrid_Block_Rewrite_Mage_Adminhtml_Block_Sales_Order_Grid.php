<?php
// BLCG_REWRITE_CODE_VERSION=1
// This file was generated automatically. Do not alter its content.

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2015 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class BL_CustomGrid_Block_Rewrite_Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    private $_blcg_gridModel   = null;
    private $_blcg_typeModel   = null;
    private $_blcg_filterParam = null;
    private $_blcg_exportInfos = null;
    private $_blcg_exportedCollection    = null;
    private $_blcg_holdPrepareCollection = false;
    private $_blcg_prepareEventsEnabled  = true;
    private $_blcg_defaultParameters     = array();
    private $_blcg_collectionCallbacks   = array(
        'before_prepare'     => array(),
        'after_prepare'      => array(),
        'before_set_filters' => array(),
        'after_set_filters'  => array(),
        'before_set'         => array(),
        'after_set'          => array(),
        'before_export_load' => array(),
        'after_export_load'  => array(),
    );
    private $_blcg_additionalAttributes = array();
    private $_blcg_mustSelectAdditionalAttributes   = false;
    
    public function getModuleName()
    {
        $module = $this->getData('module_name');
        
        if (is_null($module)) {
            if (!$class = get_parent_class($this)) {
                $class = get_class($this);
            }
            $module = substr($class, 0, strpos($class, '_Block'));
            $this->setData('module_name', $module);
        }
        
        return $module;
    }
    
    public function setCollection($collection)
    {
        if (!is_null($this->_blcg_typeModel)) {
            $this->_blcg_typeModel->beforeGridSetCollection($this, $collection);
        }
        $this->_blcg_launchCollectionCallbacks('before_set', array($this, $collection));
        $return = parent::setCollection($collection);
        $this->_blcg_launchCollectionCallbacks('after_set', array($this, $collection));
        if (!is_null($this->_blcg_typeModel)) {
            $this->_blcg_typeModel->afterGridSetCollection($this, $collection);
        }
        return $return;
    }
    
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($this->_blcg_mustSelectAdditionalAttributes
            && ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract)
            && count($this->_blcg_additionalAttributes)) {
            $this->_blcg_mustSelectAdditionalAttributes = false;
            foreach ($this->_blcg_additionalAttributes as $attr) {
                $collection->joinAttribute($attr['alias'], $attr['attribute'], $attr['bind'], $attr['filter'], $attr['join_type'], $attr['store_id']);
            }
        }
        return $collection;
    }
    
    protected function _setFilterValues($data)
    {
        if ($this->_blcg_holdPrepareCollection) {
            return $this;
        } else {
            if (!is_null($this->_blcg_gridModel)) {
                $data = $this->_blcg_gridModel->verifyGridBlockFilters($this, $data);
            }
            $this->_blcg_launchCollectionCallbacks('before_set_filters', array($this, $this->_collection, $data));
            $return = parent::_setFilterValues($data);
            $this->_blcg_launchCollectionCallbacks('after_set_filters', array($this, $this->_collection, $data));
            return $return;
        }
    }
    
       protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id',array('method'));
        $collection->getSelect()->joinLeft('sales_order_stock_location', 'main_table.entity_id = sales_order_stock_location.order_id',array('stock_location'));
        $collection->getSelect()->joinLeft(array('sales_flat_order_address'=>'sales_flat_order_address'),'main_table.entity_id = sales_flat_order_address.parent_id AND sales_flat_order_address.address_type="shipping"',array('sales_flat_order_address.region'));
        
        /*$collection->getSelect()->join(
          array('customer'=> 'customer_entity'),
          'customer.entity_id = main_table.customer_id',
          array('customer.group_id')
        );*/

        $collection->getSelect()->join(
          array('oe'=>'sales_flat_order'),
          'oe.entity_id=main_table.entity_id',
          array('oe.customer_group_id')
        );
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
            'sortable'  =>false,
            'filter' => false, 
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
        
        $optionsArrayLocation = array( 'Kurla Warehouse' => 'Kurla Warehouse', 'Bhiwandi Warehouse' => 'Bhiwandi Warehouse', 'Bangalore YCH HUB' => 'Bangalore YCH HUB','Bangalore Proconnect HUB'=>'Bangalore Proconnect HUB','Andheri HO'=>'Andheri HO','MP Warehouse'=>'MP Warehouse','RJ Warehouse'=>'RJ Warehouse','OR Warehouse'=>'OR Warehouse','AS Warehouse'=>'AS Warehouse','CG Warehouse'=>'CG Warehouse');
        
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
    
    public function _exportIterateCollection($callback, array $args)
    {
        if (!is_array($this->_blcg_exportInfos)) {
            return parent::_exportIterateCollection($callback, $args);
        } else {
            if (!is_null($this->_blcg_exportedCollection)) {
                $originalCollection = $this->_blcg_exportedCollection;
            } else {
                $originalCollection = $this->getCollection();
            }
            if ($originalCollection->isLoaded()) {
                Mage::throwException(Mage::helper('customgrid')->__('This grid does not seem to be compatible with the custom export. If you wish to report this problem, please indicate this class name : "%s"', get_class($this)));
            }
            
            $exportPageSize = (isset($this->_exportPageSize) ? $this->_exportPageSize : 1000);
            $infos = $this->_blcg_exportInfos;
            $total = (isset($infos['custom_size']) ?
                intval($infos['custom_size']) :
                (isset($infos['size']) ? intval($infos['size']) : $exportPageSize));
                
            if ($total <= 0) {
                return;
            }
            
            $fromResult = (isset($infos['from_result']) ? intval($infos['from_result']) : 1);
            $pageSize   = min($total, $exportPageSize);
            $page       = ceil($fromResult/$pageSize);
            $pitchSize  = ($fromResult > 1 ? $fromResult-1 - ($page-1)*$pageSize : 0);
            $break      = false;
            $count      = null;
            
            while ($break !== true) {
                $collection = clone $originalCollection;
                $collection->setPageSize($pageSize);
                $collection->setCurPage($page);
                
                if (!is_null($this->_blcg_typeModel)) {
                    $this->_blcg_typeModel->beforeGridExportLoadCollection($this, $collection);
                }
                $this->_blcg_launchCollectionCallbacks('before_export_load', array($this, $collection, $page, $pageSize));
                $collection->load();
                $this->_blcg_launchCollectionCallbacks('after_export_load', array($this, $collection, $page, $pageSize));
                if (!is_null($this->_blcg_typeModel)) {
                    $this->_blcg_typeModel->afterGridExportLoadCollection($this, $collection);
                }
                
                if (is_null($count)) {
                    $count = $collection->getSize();
                    $total = min(max(0, $count-$fromResult+1), $total);
                    if ($total == 0) {
                        $break = true;
                        continue;
                    }
                    $first = true;
                    $exported = 0;
                }
                
                $page++;
                $i = 0;
                
                foreach ($collection as $item) {
                    if ($first) {
                        if ($i++ < $pitchSize) {
                            continue;
                        } else {
                            $first = false;
                        }
                    }
                    if (++$exported > $total) {
                        $break = true;
                        break;
                    }
                    call_user_func_array(array($this, $callback), array_merge(array($item), $args));
                }
            }
        }
    }
    
    public function blcg_isExport()
    {
        return $this->_isExport;
    }
    
    public function setDefaultPage($page)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $page = $this->_blcg_gridModel->getGridBlockDefaultParamValue('page', $page, null, false, $this->_defaultPage);
        }
        return parent::setDefaultPage($page);
    }
    
    public function setDefaultLimit($limit)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $limit = $this->_blcg_gridModel->getGridBlockDefaultParamValue('limit', $limit, null, false, $this->_defaultLimit);
        }
        return parent::setDefaultLimit($limit);
    }
    
    public function setDefaultSort($sort)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $sort = $this->_blcg_gridModel->getGridBlockDefaultParamValue('sort', $sort, null, false, $this->_defaultSort);
        }
        return parent::setDefaultSort($sort);
    }
    
    public function setDefaultDir($dir)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $dir = $this->_blcg_gridModel->getGridBlockDefaultParamValue('dir', $dir, null, false, $this->_defaultDir);
        }
        return parent::setDefaultDir($dir);
    }
    
    public function setDefaultFilter($filter)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $filter = $this->_blcg_gridModel->getGridBlockDefaultParamValue('filter', $filter, null, false, $this->_defaultFilter);
        }
        return parent::setDefaultFilter($filter);
    }
    
    public function blcg_setDefaultPage($page)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $page = $this->_blcg_gridModel->getGridBlockDefaultParamValue('page', $this->_defaultPage, $page, true);
        }
        return parent::setDefaultPage($page);
    }
    
    public function blcg_setDefaultLimit($limit, $forced=false)
    {
        if (!$forced && !is_null($this->_blcg_gridModel)) {
            $limit = $this->_blcg_gridModel->getGridBlockDefaultParamValue('limit', $this->_defaultLimit, $limit, true);
        }
        return parent::setDefaultLimit($limit);
    }
    
    public function blcg_setDefaultSort($sort)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $sort = $this->_blcg_gridModel->getGridBlockDefaultParamValue('sort', $this->_defaultSort, $sort, true);
        }
        return parent::setDefaultSort($sort);
    }
    
    public function blcg_setDefaultDir($dir)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $dir = $this->_blcg_gridModel->getGridBlockDefaultParamValue('dir', $this->_defaultDir, $dir, true);
        }
        return parent::setDefaultDir($dir);
    }
    
    public function blcg_setDefaultFilter($filter)
    {
        if (!is_null($this->_blcg_gridModel)) {
            $filter = $this->_blcg_gridModel->getGridBlockDefaultParamValue('filter', $this->_defaultFilter, $filter, true);
        }
        return parent::setDefaultFilter($filter);
    }
    
    public function blcg_setGridModel($model)
    {
        $this->_blcg_gridModel = $model;
        return $this;
    }
    
    public function blcg_getGridModel()
    {
        return $this->_blcg_gridModel;
    }
    
    public function blcg_setTypeModel($model)
    {
        $this->_blcg_typeModel = $model;
        return $this;
    }
    
    public function blcg_setFilterParam($param)
    {
        $this->_blcg_filterParam = $param;
        return $this;
    }
    
    public function blcg_getFilterParam()
    {
        return $this->_blcg_filterParam;
    }
    
    public function blcg_setExportInfos($infos)
    {
        $this->_blcg_exportInfos = $infos;
    }
    
    public function blcg_getStore()
    {
        if (method_exists($this, '_getStore')) {
            return $this->_getStore();
        }
        $storeId = (int)$this->getRequest()->getParam(Mage::helper('customgrid/config')->getStoreParameter('store'), 0);
        return Mage::app()->getStore($storeId);
    }
    
    public function blcg_getSaveParametersInSession()
    {
        return $this->_saveParametersInSession;
    }
    
    public function blcg_getSessionParamKey($name)
    {
        return $this->getId().$name;
    }
    
    public function blcg_getPage()
    {
        if ($this->getCollection() && $this->getCollection()->isLoaded()) {
            return $this->getCollection()->getCurPage();
        }
        return $this->getParam($this->getVarNamePage(), $this->_defaultPage);
    }
    
    public function blcg_getLimit()
    {
        return $this->getParam($this->getVarNameLimit(), $this->_defaultLimit);
    }
    
    public function blcg_getSort($checkExists=true)
    {
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        if (!$checkExists || (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex())) {
            return $columnId;
        }
        return null;
    }
    
    public function blcg_getDir()
    {
        if ($this->blcg_getSort()) {
            return (strtolower($this->getParam($this->getVarNameDir(), $this->_defaultDir)) == 'desc') ? 'desc' : 'asc';
        }
        return null;
    }
    
    public function blcg_getCollectionSize()
    {
        if ($this->getCollection()) {
            return $this->getCollection()->getSize();
        }
        return null;
    }
    
    public function blcg_addAdditionalAttribute(array $attribute)
    {
        $this->_blcg_additionalAttributes[] = $attribute;
        return $this;
    }
    
    public function blcg_setExportedCollection($collection)
    {
        $this->_blcg_exportedCollection = $collection;
        return $this;
    }
    
    public function blcg_holdPrepareCollection()
    {
        $this->_blcg_holdPrepareCollection = true;
        return $this;
    }
    
    public function blcg_finishPrepareCollection()
    {
        if ($this->getCollection()) {
            $this->_blcg_holdPrepareCollection = false;
            $this->_blcg_prepareEventsEnabled  = false;
            $this->_blcg_mustSelectAdditionalAttributes = true;
            $this->_prepareCollection();
        }
        return $this;
    }
    
    public function blcg_removeColumn($id)
    {
        if (array_key_exists($id, $this->_columns)) {
            unset($this->_columns[$id]);
            if ($this->_lastColumnId == $id) {
                $keys = array_keys($this->_columns);
                $this->_lastColumnId = array_pop($keys);
            }
        }
        return $this;
    }
    
    public function blcg_resetColumnsOrder()
    {
        $this->_columnsOrder = array();
        return $this;
    }
    
    public function blcg_addCollectionCallback($type, $callback, $params=array(), $addNative=true)
    {
        $this->_blcg_collectionCallbacks[$type][] = array(
            'callback'   => $callback,
            'params'     => $params,
            'add_native' => $addNative,
        );
        end($this->_blcg_collectionCallbacks[$type]);
        $key = key($this->_blcg_collectionCallbacks);
        reset($this->_blcg_collectionCallbacks);
        return $key;
    }
    
    public function blcg_removeCollectionCallback($type, $id)
    {
        if (isset($this->_blcg_collectionCallbacks[$type][$id])) {
            unset($this->_blcg_collectionCallbacks[$type][$id]);
        }
        return $this;
    }
    
    protected function _blcg_launchCollectionCallbacks($type, $params=array())
    {
        foreach ($this->_blcg_collectionCallbacks[$type] as $callback) {
            call_user_func_array(
                $callback['callback'],
                array_merge(
                    array_values($callback['params']),
                    ($callback['add_native']? array_values($params) : array())
                )
            );
        }
        return $this;
    }
    
    protected function getStockLocationOptions()
    {
        return Mage::helper('stocklocation')->getStockLocationOptions();
    }
}