<?php

class Neo_Asmdetail_Block_Adminhtml_Asm_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("asmGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("asmdetail/asm")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("asmdetail")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("asmdetail")->__("ARM Name"),
				"index" => "name",
				));

				$this->addColumn("enabled", array(
				"header" => Mage::helper("asmdetail")->__("Is Active"),
				"index" => "enabled",
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
			$this->getMassactionBlock()->addItem('remove_asm', array(
					 'label'=> Mage::helper('asmdetail')->__('Remove Asm'),
					 'url'  => $this->getUrl('*/adminhtml_asm/massRemove'),
					 'confirm' => Mage::helper('asmdetail')->__('Are you sure?')
				));
			return $this;
		}
			

}