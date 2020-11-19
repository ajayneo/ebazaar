<?php

class Neo_Ebautomation_Block_Adminhtml_Priceupdatelog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("priceupdatelogGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("ebautomation/priceupdatelog")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("ebautomation")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));

				$this->addColumn("price", array(
				"header" => Mage::helper("ebautomation")->__("Price"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "text",
				"index" => "price",
				));

				$this->addColumn("user", array(
				"header" => Mage::helper("ebautomation")->__("User"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "text",
				"index" => "user",
				));

				

				$this->addColumn("created_at", array(
				"header" => Mage::helper("ebautomation")->__("Created At"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "text",
				"index" => "created_at",
				));
                

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		

}