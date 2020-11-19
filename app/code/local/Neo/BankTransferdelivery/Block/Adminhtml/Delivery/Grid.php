<?php

class Neo_BankTransferdelivery_Block_Adminhtml_Delivery_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("deliveryGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("banktransferdelivery/delivery")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("banktransferdelivery")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("order_id", array(
				"header" => Mage::helper("banktransferdelivery")->__("Order Id"),
				"index" => "order_id",
				));
				$this->addColumn("order_num", array(
				"header" => Mage::helper("banktransferdelivery")->__("Order Number"),
				"index" => "order_num",
				));
				$this->addColumn("delivery", array(
				"header" => Mage::helper("banktransferdelivery")->__("Delivery"),
				"index" => "delivery",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		

}