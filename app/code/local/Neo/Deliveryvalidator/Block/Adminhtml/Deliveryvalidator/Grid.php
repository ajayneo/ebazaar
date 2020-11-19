<?php

class Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("deliveryvalidatorGrid");
				$this->setDefaultSort("pincode_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("deliveryvalidator/deliveryvalidator")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("pincode_id", array(
				"header" => Mage::helper("deliveryvalidator")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "pincode_id",
				));
                
				$this->addColumn("rules", array(
				"header" => Mage::helper("deliveryvalidator")->__("Delivery Days"),
				"index" => "rules",
				));
						$this->addColumn('status', array(
						'header' => Mage::helper('deliveryvalidator')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Grid::getOptionArray2(),				
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
			$this->setMassactionIdField('pincode_id');
			$this->getMassactionBlock()->setFormFieldName('pincode_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_deliveryvalidator', array(
					 'label'=> Mage::helper('deliveryvalidator')->__('Remove Deliveryvalidator'),
					 'url'  => $this->getUrl('*/adminhtml_deliveryvalidator/massRemove'),
					 'confirm' => Mage::helper('deliveryvalidator')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='Enable';
			$data_array[1]='Disable';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}