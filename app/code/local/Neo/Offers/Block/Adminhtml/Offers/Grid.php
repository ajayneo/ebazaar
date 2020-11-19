<?php

class Neo_Offers_Block_Adminhtml_Offers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("offersGrid");
				$this->setDefaultSort("cashback_offers_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("offers/offers")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("cashback_offers_id", array(
				"header" => Mage::helper("offers")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "cashback_offers_id",
				));
                
				$this->addColumn("offer", array(
				"header" => Mage::helper("offers")->__("Offer"),
				"index" => "offer",
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
			$this->setMassactionIdField('cashback_offers_id');
			$this->getMassactionBlock()->setFormFieldName('cashback_offers_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_offers', array(
					 'label'=> Mage::helper('offers')->__('Remove Offers'),
					 'url'  => $this->getUrl('*/adminhtml_offers/massRemove'),
					 'confirm' => Mage::helper('offers')->__('Are you sure?')
				));
			return $this;
		}
			

}