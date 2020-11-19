<?php
	class Neo_Affiliate_Block_Adminhtml_Manage_Affiliate_Grid extends Mage_Adminhtml_Block_Widget_Grid
	{
		public function _construct(){
			parent::_construct();
			$this->setId('AffiliateGrid');
			$this->setDefaultSort('affiliate_id');
			$this->setDefaultDir('DESC');
        	$this->setSaveParametersInSession(true);
        	//$this->setUseAjax(true);
		}

		protected function _prepareCollection()
	    {
			$collection = Mage::getModel('neoaffiliate/affiliate')->getCollection();
			$collection->join(array('og' =>'sales/order_grid'), 'main_table.order_id = og.entity_id', array('billing_name','created_at','base_grand_total','grand_total','status'));
	        //$collection->getSelect()->joinLeft(array('st' =>'sales/shipment_track'), 'main_table.order_id = st.order_id', array('track_number'));

			//$collection->getSelect()->joinLeft(array('sfi' =>'sales_flat_invoice'), 'main_table.order_id = sfi.order_id','increment_id as invoice_id');
			//$collection->getSelect()->joinLeft(array('sfs' =>'sales_flat_shipment'), 'main_table.order_id = sfs.order_id','increment_id as shipment_id');
			//$collection->getSelect()->group('main_table.affiliate_id');
			//$collection->getSelect()->distinct(true);
			//$collection->getSelect()->distinct('main_table.affiliate_id');
	        //echo "<pre>"; print_r($collection->getData()); exit;

	        //$collection->getSelect()->distinct();
	        //$collection->getSelect()->group('main_table.affiliate_id');
			//$collection->getSelect()->joinLeft(array('sfst' =>'sales_flat_shipment_track'), 'main_table.order_id = sfst.order_id', array('track_number'));


	        //echo "<pre>"; print_r(get_class_methods(get_class($collection))); exit;
	        //echo $collection->getSelect()->assemble(); exit;
        	$this->setCollection($collection);
        	return parent::_prepareCollection();
	    }

		protected function _prepareColumns()
	    {
	        $this->addColumn('affiliate_id', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Serial No.'),
	            'align'     =>'right',
	            'width'     => '50px',
	            'index'     => 'affiliate_id',
	        ));

	        // $this->addColumn('track_number', array(
	        //     'header'    => Mage::helper('neo_affiliate')->__('Track No.'),
	        //     'align'     =>'right',
	        //     'width'     => '50px',
	        //     'index'     => 'track_number',
	        // ));

	        // $this->addColumn('invoice_id', array(
	        //     'header'    => Mage::helper('neo_affiliate')->__('Invoice No.'),
	        //     'align'     =>'right',
	        //     'width'     => '50px',
	        //     'index'     => 'invoice_id',
	        //     'column_css_class' => 'no-display',
	        //     'header_css_class' => 'no-display',
	        // ));

	        // $this->addColumn('shipment_id', array(
	        //     'header'    => Mage::helper('neo_affiliate')->__('Shipment No.'),
	        //     'align'     =>'right',
	        //     'width'     => '50px',
	        //     'index'     => 'shipment_id',
	        // ));
	 
	        $this->addColumn('order_no', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Order Number'),
	            'align'     =>'left',
	            'index'     => 'order_no',
	        ));
			
			$this->addColumn('created_at', array(
	            'header' => Mage::helper('neo_affiliate')->__('Purchased On'),
	            'index' => 'created_at',
	            'type' => 'datetime',
	            'width' => '100px',
	        ));
			
			$this->addColumn('repcode', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Repcode'),
	            'align'     =>'left',
	            'index'     => 'repcode',
	        ));

	        $this->addColumn('affiliate_name', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Affiliate Name'),
	            'align'     =>'left',
	            'index'     => 'affiliate_name',
	        ));
			
			$this->addColumn('category_name', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Category Name'),
	            'align'     =>'left',
	            'index'     => 'category_name',
	        ));
			
			$this->addColumn('product_name', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Product Name'),
	            'align'     => 'left',
	            'width'     => '200px',
	            'index'     => 'product_name',
	        ));

	        $this->addColumn('qty_ordered', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Qty Ordered'),
	            'align'     => 'left',
	            'width'     => '200px',
	            'index'     => 'qty_ordered',
	        ));
			
			$this->addColumn('item_price_excl_tax', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Item Price'),
	            'align'     =>'left',
	            'index'     => 'item_price_excl_tax',
	        ));
			
			$this->addColumn('tax_amount', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Tax Amount'),
	            'align'     =>'left',
	            'index'     => 'tax_amount',
	        ));
			
			$this->addColumn('row_total', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Gross Total'),
	            'align'     =>'left',
	            'index'     => 'row_total',
	        ));

			$this->addColumn('order_total', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Order Total'),
	            'align'     =>'left',
	            'index'     => 'order_total',
	        ));
			
			$this->addColumn('aff_customer_name', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Customer Name'),
	            'align'     =>'left',
	            'index'     => 'aff_customer_name',
	        ));
			
			$this->addColumn('aff_customer_email', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Customer Email'),
	            'align'     =>'left',
	            'index'     => 'aff_customer_email',
	        ));
			
			$this->addColumn('aff_customer_mobile', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Customer Mobile'),
	            'align'     =>'left',
	            'index'     => 'aff_customer_mobile',
	        ));

	        /*$this->addColumn('tracking_title',array(
	        	'header'	=> Mage::helper('neo_affiliate')->__('Courier Name'),
	        	'align'		=> 'left',
	        	'index'		=> 'tracking_title',
	        ));

	        $this->addColumn('tracking_no',array(
	        	'header'	=> Mage::helper('neo_affiliate')->__('AWB Number'),
	        	'align'		=> 'left',
	        	'index'		=> 'tracking_no',
	        ));*/

	        $this->addColumn('iscstused', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Is CST USED'),
	            'align'     =>'left',
	            'index'     => 'iscstused',
	        ));

		//category_name
		//product_name
		//product_price
		//order_total
		//customer_name
		//customer_email
		//customer_mobile
	        /*$this->addColumn('billing_name', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Bill to Name'),
	            'align'     =>'left',
	            'index'     => 'billing_name',
	        ));*/

	        

	        /*$this->addColumn('base_grand_total', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Base Grand Total'),
	            'align'     =>'left',
	            'index'     => 'base_grand_total',
	        ));

	        $this->addColumn('grand_total', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Grand Total'),
	            'align'     =>'left',
	            'index'     => 'grand_total',
	        ));*/

	        $this->addColumn('status', array(
	            'header'    => Mage::helper('neo_affiliate')->__('Status'),
	            'align'     =>'left',
	            'index'     => 'status',
	            'type'  => 'options',
	            'width' => '70px',
	            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
	        ));
			
			$this->addColumn('action',array(
                'header'    =>    Mage::helper('neo_affiliate')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getOrderId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('neo_affiliate')->__('View'),
                        'url'        => array('base'=>'*/sales_order/view'),
                        'field'        => 'order_id',
                        'target' => '_blank'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));
		
			$this->addExportType('*/*/exportCsv', Mage::helper('neo_affiliate')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('neo_affiliate')->__('Excel'));
	 
	        return parent::_prepareColumns();
	    }

		public function getRowUrl($row)
	    {
	       if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            	return $this->getUrl('*/sales_order/view', array('order_id' => $row->getOrderId()));
        	}
	    }

	    protected function _prepareMassaction()
    	{
	        $this->setMassactionIdField('affiliate_id');
	        $this->getMassactionBlock()->setFormFieldName('order_ids');
	        $this->getMassactionBlock()->setUseSelectAll(true);

	        $this->getMassactionBlock()->addItem('orderexport', array(
	            'label'=> Mage::helper('sales')->__('Export Orders'),
	            'url'  => $this->getUrl('*/sales_order_export/csvexportcform'),
	        ));
	        
	    } 
	}
?>