<?php

class Neo_Supersale_Block_Adminhtml_Super_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("superGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("supersale/super")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("supersale")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));

				$this->addColumn("name", array(
				"header" => Mage::helper("supersale")->__("Name"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "text",
				"index" => "name",
				));

				$this->addColumn("mobile", array(
				"header" => Mage::helper("supersale")->__("Mobile"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "text",
				"index" => "mobile",
				));
                
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   //return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_super', array(
					 'label'=> Mage::helper('supersale')->__('Remove Super'),
					 'url'  => $this->getUrl('*/adminhtml_super/massRemove'),
					 'confirm' => Mage::helper('supersale')->__('Are you sure?')
				));
			return $this;
		}
			

}