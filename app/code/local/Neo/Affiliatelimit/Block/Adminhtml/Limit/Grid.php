	<?php

	class Neo_Affiliatelimit_Block_Adminhtml_Limit_Grid extends Mage_Adminhtml_Block_Widget_Grid
	{

			public function __construct()
			{
					parent::__construct();
					$this->setId("limitGrid");
					$this->setDefaultSort("id");
					$this->setDefaultDir("DESC");
					$this->setSaveParametersInSession(true);
			}

			protected function _prepareCollection()
			{
					$collection = Mage::getModel("affiliatelimit/limit")->getCollection();
					$this->setCollection($collection);
					return parent::_prepareCollection();
			}
			protected function _prepareColumns()
			{
				$this->addColumn("id", array(
				"header" => Mage::helper("affiliatelimit")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn('email', array(
				'header' => Mage::helper('affiliatelimit')->__('Email'),
				'index' => 'email',
				'type' => 'options',
				'options'=>Neo_Affiliatelimit_Block_Adminhtml_Limit_Grid::getOptionArray0(),				
				));
				
				$this->addColumn("credit", array(
				"header" => Mage::helper("affiliatelimit")->__("Credit"),
				"index" => "credit",
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
				$this->getMassactionBlock()->addItem('remove_limit', array(
						 'label'=> Mage::helper('affiliatelimit')->__('Remove Limit'),
						 'url'  => $this->getUrl('*/adminhtml_limit/massRemove'),
						 'confirm' => Mage::helper('affiliatelimit')->__('Are you sure?')
					));
				return $this;
			}
				
			static public function getOptionArray0()
			{
				$customerCollection = Mage::getModel('customer/customer')->getCollection();
				
				$data_array=array(); 
				foreach ($customerCollection->getData() as $key => $value) {
					if($value['group_id'] == 4){
						$data_array[$value['entity_id']] = $value['email'];
					}
				}
	            
	            return($data_array);
			}

			static public function getValueArray0()
			{
	            $data_array=array();
				foreach(Neo_Affiliatelimit_Block_Adminhtml_Limit_Grid::getOptionArray0() as $k=>$v){
	               $data_array[]=array('value'=>$k,'label'=>$v);		
				}
	            return($data_array);

			}
			

	}