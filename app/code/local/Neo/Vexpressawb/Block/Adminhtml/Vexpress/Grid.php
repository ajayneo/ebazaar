<?php

class Neo_Vexpressawb_Block_Adminhtml_Vexpress_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("vexpressGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("vexpressawb/vexpress")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{

			$this->addColumn("awb", array(
				"header" => Mage::helper("vexpressawb")->__("AWB"),
				"align" =>"right",
				"width" => "250px",
			    "type" => "text",
				"index" => "awb",
			));

			$this->addColumn("status", array(
				"header" => Mage::helper("vexpressawb")->__("Status"),
				"align" =>"right",
				"width" => "250px",
			    "type" => "text",
				"index" => "status",
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
			$this->getMassactionBlock()->addItem('remove_vexpress', array(
				 'label'=> Mage::helper('vexpressawb')->__('Remove Vexpress'),
				 'url'  => $this->getUrl('*/adminhtml_vexpress/massRemove'),
				 'confirm' => Mage::helper('vexpressawb')->__('Are you sure?')
			));
			return $this;
		}
			

}