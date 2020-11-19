<?php

class Neo_Vowdelight_Block_Adminhtml_Vowdelight_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("vowdelightGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("vowdelight/vowdelight")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("vowdelight")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("request_id", array(
				"header" => Mage::helper("vowdelight")->__("Request ID"),
				"index" => "request_id",
				));
				$this->addColumn("sku", array(
				"header" => Mage::helper("vowdelight")->__("Sku"),
				"index" => "sku",
				));
				$this->addColumn("old_order_no", array(
				"header" => Mage::helper("vowdelight")->__("Old Order No"),
				"index" => "old_order_no",
				));
				$this->addColumn("old_imei_no", array(
				"header" => Mage::helper("vowdelight")->__("Old IMEI No"),
				"index" => "old_imei_no",
				));
				$this->addColumn("rvp_awb_no", array(
				"header" => Mage::helper("vowdelight")->__("RVP AWB No"),
				"index" => "rvp_awb_no",
				));
				$this->addColumn("new_order_no", array(
				"header" => Mage::helper("vowdelight")->__("New Order No"),
				"index" => "new_order_no",
				));
				$this->addColumn("new_imei_no", array(
				"header" => Mage::helper("vowdelight")->__("New IMEI No"),
				"index" => "new_imei_no",
				));
				$this->addColumn("new_awb_no", array(
				"header" => Mage::helper("vowdelight")->__("New AWB No"),
				"index" => "new_awb_no",
				));
				$this->addColumn("customer_id", array(
				"header" => Mage::helper("vowdelight")->__("Customer ID"),
				"index" => "customer_id",
				));
					$this->addColumn('created_at', array(
						'header'    => Mage::helper('vowdelight')->__('Created At'),
						'index'     => 'created_at',
						'type'      => 'datetime',
					));
					$this->addColumn('for_ship_created_at', array(
						'header'    => Mage::helper('vowdelight')->__('For Ship created At'),
						'index'     => 'for_ship_created_at',
						'type'      => 'datetime',
					));
					$this->addColumn('rev_ship_created_at', array(
						'header'    => Mage::helper('vowdelight')->__('Rev Ship created At'),
						'index'     => 'rev_ship_created_at',
						'type'      => 'datetime',
					));
		            //Start test code.
				     $this->addColumn('action',
				        array(
				      'header' => Mage::helper('vowdelight')->__('RVP'),
				      'width' => '50',
				      'type' => 'action',
				      'getter' => 'getId',
				      // 'actions' => array(
				      //        array(
				      //             'caption' => Mage::helper('vowdelight')->__('RVP'),
				      //             'url' => array('base'=> '*/*/reversePickup'),
				      //             'field' => 'id'
				      //           )),
				      'filter' => false,
				      'sortable' => false,
				      'index' => 'stores',
				      'is_system' => true,
				      'renderer'  => 'Neo_Vowdelight_Block_Adminhtml_Vowdelight_Renderer_Rvp',// THIS IS WHAT THIS POST IS ALL ABOUT
				     ));					
		            //End test code.					
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_vowdelight', array(
					 'label'=> Mage::helper('vowdelight')->__('Remove Vowdelight'),
					 'url'  => $this->getUrl('*/adminhtml_vowdelight/massRemove'),
					 'confirm' => Mage::helper('vowdelight')->__('Are you sure?')
				));
			return $this;
		}
			

}