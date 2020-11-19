<?php

class Neo_Productpricereport_Block_Adminhtml_Productpricereport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("productpricereportGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _getStore()
	    {
	        $storeId = (int) $this->getRequest()->getParam('store', 0);
	        return Mage::app()->getStore($storeId);
	    }

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("productpricereport/productpricereport")->getCollection();
	
				// $collection = Mage::getModel('catalog/product')->getCollection()
				// ->addAttributeToSelect('name')
    //             ->addAttributeToSelect('sku');

				// $collection->getSelect()->join(array('mep' => "neo_productpricereport"), "e.entity_id = mep.product_id", array('mep.*'));

				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
			$this->addColumn("id", array(
				"header" => Mage::helper("productpricereport")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
			));

			/*$link= Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit/') .'id/10717';

			$this->addColumn("product_id", array(
				"header" => Mage::helper("productpricereport")->__("Product Id"),
				"align" =>"right",
				"width" => "25px",
				"index" => "product_id",
				"type" => "action",
				'actions'  => array(
		            array(
		                'url'     => $link,
		                'caption' => $this->helper('catalog')->__('Edit'),
		            ),
		        )
			));*/

			$this->addColumn("product_id", array(
				"header" => Mage::helper("productpricereport")->__("Product Id"),
				"align" =>"right",
				"width" => "25px",
				"index" => "product_id",
				"type" => "number",
				//'frame_callback' => array($this, 'decorateRow'),
			));

			// $this->addColumn('sku', array(
	  //           'header'    => Mage::helper('Sales')->__('Sku'),
	  //           'width'     => '100px',
	  //           'index'     => 'sku',
	  //           'type'        => 'text',
	 
	  //       ));

	        $this->addColumn('name', array(
	            'header'    => Mage::helper('Sales')->__('Name'),
	            'width'     => '100px',
	            'index'     => 'name',
	            'type'        => 'text',
	 
	        ));
			$store = $this->_getStore();
	        $this->addColumn('to_price',
	            array(
	                'header'=> Mage::helper('catalog')->__('To Price'),
	                'type'  => 'price',
	                "width" => "200px",
	                'currency_code' => $store->getBaseCurrency()->getCode(),
	                'index' => 'to_price',
	        )); 

			$store = $this->_getStore();
	        $this->addColumn('from_price',
	            array(
	                'header'=> Mage::helper('catalog')->__('From Price'),
	                'type'  => 'price',
	                "width" => "200px",
	                'currency_code' => $store->getBaseCurrency()->getCode(),
	                'index' => 'from_price',
	        ));

			$this->addColumn("changed_by", array(
				"header" => Mage::helper("productpricereport")->__("Changed By"),
				"align" =>"right",
				"width" => "200px",
			    //"type" => "number",
				"index" => "changed_by",
			));

			$this->addColumn('date', array(
	            'header' => Mage::helper('sales')->__('Changed On'),
	            'index' => 'date',
	            'type' => 'datetime',
	            //'format' => 'Y-m-d h:m:s' ,//2017-04-10 17:03:22 
	            // 'format' => 'Y-m-d' ,//2014-09-26 05:51:15
	            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
	            'width' => '200px', 
	        ));

                
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("adminhtml/catalog_product/edit", array("id" => $row->getProductId()));
		}

		public function decorateRow($sVal, Mage_Core_Model_Abstract $oRow){
			$link = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit/') .'id/'.$sVal;
		    return "<a href=".$link." >$sVal </a>";
		}
		
		protected function _prepareMassaction()
		{
			//return;
			
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_productpricereport', array(
					 'label'=> Mage::helper('productpricereport')->__('Remove Productpricereport'),
					 'url'  => $this->getUrl('*/adminhtml_productpricereport/massRemove'),
					 'confirm' => Mage::helper('productpricereport')->__('Are you sure?')
				));
			return $this;
		}
			

}