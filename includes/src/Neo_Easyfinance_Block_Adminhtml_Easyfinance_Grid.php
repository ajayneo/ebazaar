<?php

class Neo_Easyfinance_Block_Adminhtml_Easyfinance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("easyfinanceGrid");
				$this->setDefaultSort("easy_finance_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("easyfinance/easyfinance")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("easy_finance_id", array(
				"header" => Mage::helper("easyfinance")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "easy_finance_id",
				));
                
				$this->addColumn("first_name", array(
				"header" => Mage::helper("easyfinance")->__("First Name"),
				"index" => "first_name",
				));
				$this->addColumn("last_name", array(
				"header" => Mage::helper("easyfinance")->__("Last Name"),
				"index" => "last_name",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("easyfinance")->__("Email Address"),
				"index" => "email",
				));
				$this->addColumn("phone", array(
				"header" => Mage::helper("easyfinance")->__("Phone"),
				"index" => "phone",
				));
				$this->addColumn("city", array(
				"header" => Mage::helper("easyfinance")->__("City"),
				"index" => "city",
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
			$this->setMassactionIdField('easy_finance_id');
			$this->getMassactionBlock()->setFormFieldName('easy_finance_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_easyfinance', array(
					 'label'=> Mage::helper('easyfinance')->__('Remove Easyfinance'),
					 'url'  => $this->getUrl('*/adminhtml_easyfinance/massRemove'),
					 'confirm' => Mage::helper('easyfinance')->__('Are you sure?')
				));
			return $this;
		}
			

}