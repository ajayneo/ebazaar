<?php

class Neo_Gadget_Block_Adminhtml_Gadget_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("gadgetGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("gadget/gadget")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
			/*$this->addColumn("id", array(
				"header" => Mage::helper("gadget")->__("ID"),
				"align" =>"right",
				"width" => "10px",
			    "type" => "number",
				"index" => "id",
			));*/

			$this->addColumn("name", array(
				"header" => Mage::helper("gadget")->__("Name"),
				"align" =>"right",
				"width" => "300px",
			    "type" => "text",
				"index" => "name",
			));


			$this->addColumn("image", array(
				"header" => Mage::helper("gadget")->__("Image"),
				"align" =>"center",
				"width" => "100px",
			    "type" => "text",
				"index" => "image",
				'filter' => false, 
				'renderer' => 'Neo_Gadget_Block_Adminhtml_Gadget_Renderer_Image',
			));

			$brands = Neo_Gadget_Model_Brands::getBrandsGrid();

			$this->addColumn("brand", array(
				"header" => Mage::helper("gadget")->__("Brand"),
				"align" =>"right",
				"width" => "300px",
			    "type" => "options",
				"index" => "brand",
				'options' => $brands,
			));

			$this->addColumn("working_price", array(
				"header" => Mage::helper("gadget")->__("Working Price"),
				"align" =>"right",
				"width" => "300px",
			    "type" => "number",
				"index" => "working_price",
			));

			$this->addColumn("non_working_price", array(
				"header" => Mage::helper("gadget")->__("Non Working Price"),
				"align" =>"right",
				"width" => "300px",
			    "type" => "number",
				"index" => "non_working_price",
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
			$this->getMassactionBlock()->addItem('remove_gadget', array(
					 'label'=> Mage::helper('gadget')->__('Remove Gadget'),
					 'url'  => $this->getUrl('*/adminhtml_gadget/massRemove'),
					 'confirm' => Mage::helper('gadget')->__('Are you sure?')
				));
			return $this;
		}
			

}