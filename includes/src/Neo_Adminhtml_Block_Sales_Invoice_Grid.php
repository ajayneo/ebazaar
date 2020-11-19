<?php
    class Neo_Adminhtml_Block_Sales_Invoice_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
    {
    	protected function _prepareCollection()
	    {
	        $collection = Mage::getResourceModel($this->_getCollectionClass());
			
			//Start by Pradeep Sanku on 18June2015 for adding track number.
            $collection->getSelect()->join(
              array('sfst'=>'sales_flat_shipment_track'),
              'sfst.invoice_id=main_table.entity_id',
              array('sfst.track_number')
            );
            //End by Pradeep Sanku on 18June2015 for adding track number.
	        $this->setCollection($collection);
	        return parent::_prepareCollection();
	    }
		
		protected function _prepareColumns()
	    {
	        $this->addColumn('increment_id', array(
	            'header'    => Mage::helper('sales')->__('Invoice #'),
	            'index'     => 'increment_id',
	            'type'      => 'text',
	        ));
	
	        $this->addColumn('created_at', array(
	            'header'    => Mage::helper('sales')->__('Invoice Date'),
	            'index'     => 'created_at',
	            'type'      => 'datetime',
	        ));
	
	        $this->addColumn('order_increment_id', array(
	            'header'    => Mage::helper('sales')->__('Order #'),
	            'index'     => 'order_increment_id',
	            'type'      => 'text',
	        ));
	
	        $this->addColumn('order_created_at', array(
	            'header'    => Mage::helper('sales')->__('Order Date'),
	            'index'     => 'order_created_at',
	            'type'      => 'datetime',
	        ));
	
	        $this->addColumn('billing_name', array(
	            'header' => Mage::helper('sales')->__('Bill to Name'),
	            'index' => 'billing_name',
	        ));

			// column added by pradeep sanku on the 18th june 2015			
			$this->addColumn('sfst.track_number', array(
	            'header' => Mage::helper('sales')->__('Track Number'),
	            'index' => 'sfst.track_number',
	        ));
			// column added by pradeep sanku on the 18th june 2015
	
	        $this->addColumn('state', array(
	            'header'    => Mage::helper('sales')->__('Status'),
	            'index'     => 'state',
	            'type'      => 'options',
	            'options'   => Mage::getModel('sales/order_invoice')->getStates(),
	        ));
	
	        $this->addColumn('grand_total', array(
	            'header'    => Mage::helper('customer')->__('Amount'),
	            'index'     => 'grand_total',
	            'type'      => 'currency',
	            'align'     => 'right',
	            'currency'  => 'order_currency_code',
	        ));
	
	        $this->addColumn('action',
	            array(
	                'header'    => Mage::helper('sales')->__('Action'),
	                'width'     => '50px',
	                'type'      => 'action',
	                'getter'     => 'getId',
	                'actions'   => array(
	                    array(
	                        'caption' => Mage::helper('sales')->__('View'),
	                        'url'     => array('base'=>'*/sales_invoice/view'),
	                        'field'   => 'invoice_id'
	                    )
	                ),
	                'filter'    => false,
	                'sortable'  => false,
	                'is_system' => true
	        ));
	
	        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
	        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
	
	        return parent::_prepareColumns();
	    }
		
		
    }
?>