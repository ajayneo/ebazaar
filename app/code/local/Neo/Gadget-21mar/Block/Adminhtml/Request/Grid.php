<?php

class Neo_Gadget_Block_Adminhtml_Request_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("requestGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("gadget/request")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("gadget")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                

                $this->addColumn("program", array(
				"header" => Mage::helper("gadget")->__("Program"),
				"index" => "program",
				));

                $this->addColumn("internal_order_id", array(
				"header" => Mage::helper("gadget")->__("Internal Order Id"),
				"index" => "internal_order_id",
				));
				
				$this->addColumn("sku", array(
				"header" => Mage::helper("gadget")->__("SKU"),
				"index" => "sku",
				));

				$this->addColumn("proname", array(
				"header" => Mage::helper("gadget")->__("Pro Name"),
				"index" => "proname",
				));

				$this->addColumn("price", array(
				"header" => Mage::helper("gadget")->__("Pro Price"),
				"index" => "price",
				));

				/*$this->addColumn("brand", array(
				"header" => Mage::helper("gadget")->__("Pro Brand"),
				"index" => "brand",
				));*/

				/*$this->addColumn("option", array(
				"header" => Mage::helper("gadget")->__("Pro Option"),
				"index" => "option",
				'filter' => false, 
				'renderer' => 'Neo_Gadget_Block_Adminhtml_Request_Renderer_Option',
				));

				$this->addColumn("image", array(
				"header" => Mage::helper("gadget")->__("Pro Image"),
				"index" => "image",
				'filter' => false, 
				'renderer' => 'Neo_Gadget_Block_Adminhtml_Request_Renderer_Image',
				));*/

				/*$this->addColumn("pincode", array(
				"header" => Mage::helper("gadget")->__("User Pincode"),
				"index" => "pincode",
				));*/

				/*$this->addColumn("city", array(
				"header" => Mage::helper("gadget")->__("User City"),
				"index" => "city",
				));*/

				/*$this->addColumn("quikr-id", array(
				"header" => Mage::helper("gadget")->__("Quikr Id"),
				"index" => "quikr-id",
				));*/

				/*$this->addColumn("landmark", array(
				"header" => Mage::helper("gadget")->__("Landmark"),
				"index" => "landmark",
				));*/

				/*$this->addColumn("address", array(
				"header" => Mage::helper("gadget")->__("Address"),   
				"index" => "address",
				));*/

				$this->addColumn("email", array(
				"header" => Mage::helper("gadget")->__("User Email"),
				"index" => "email",
				));

				
				$this->addColumn("awb_number", array(
				"header" => Mage::helper("gadget")->__("AWB Number"),
				"index" => "awb_number",
				));

				$this->addColumn("serial_number", array(
				"header" => Mage::helper("gadget")->__("Serial No"),
				"index" => "serial_number",
				));

				$this->addColumn("created_at", array(
				"header" => Mage::helper("gadget")->__("Created At"),
				"index" => "created_at",
				));

				$this->addColumn("updated-at", array(
				"header" => Mage::helper("gadget")->__("Updated At"),
				"index" => "updated-at",
				));

				/*$this->addColumn("mobile", array(
				"header" => Mage::helper("gadget")->__("User Mobile"),
				"index" => "mobile",
				));*/
				
				// $optionsArray = array('pending' => 'Pending', 'closed' => 'Closed');
				$optionsArray = Mage::helper('gadget')->getGadgetStatusOptions();

				$this->addColumn('status', array(
		            'header' => Mage::helper('gadget')->__('Status'), 
		            'index' => 'status',
		            'type'  => 'options',
		            'width' => '70px',
		            'options' => $optionsArray,
		            'filter_index'=>'status', 
		        ));


				$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
				$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId())); 
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');  
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_request', array(
					 'label'=> Mage::helper('gadget')->__('Remove Request'),
					 'url'  => $this->getUrl('*/adminhtml_request/massRemove'),
					 'confirm' => Mage::helper('gadget')->__('Are you sure?')
				));
			$this->getMassactionBlock()->addItem('export_request', array(
					 'label'=> Mage::helper('gadget')->__('Export Request'),
					 'url'  => $this->getUrl('*/adminhtml_request/massExport') 
				));
			return $this;
		}
			

}