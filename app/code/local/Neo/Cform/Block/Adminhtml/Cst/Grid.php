<?php

class Neo_Cform_Block_Adminhtml_Cst_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("cstGrid");
				$this->setDefaultSort("neocform_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("cform/cst")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("neocform_id", array(
				"header" => Mage::helper("cform")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "neocform_id",
				));
                
						$this->addColumn('state', array(
						'header' => Mage::helper('cform')->__('State'),
						'index' => 'state',
						'type' => 'options',
						'options'=>Neo_Cform_Block_Adminhtml_Cst_Grid::getOptionArray0(),				
						));
						
				// $this->addColumn("amount", array(
				// "header" => Mage::helper("cform")->__("Amount"),
				// "index" => "amount",
				// ));
				$this->addColumn("min_amount", array(
				"header" => Mage::helper("cform")->__("Min.Amount"),
				"index" => "min_amount",
				));
				$this->addColumn("max_amount", array(
				"header" => Mage::helper("cform")->__("Max.Amount"),
				"index" => "max_amount",
				));
				$this->addColumn("category", array(
				"header" => Mage::helper("cform")->__("Category"),
				"index" => "category",
				));
				$this->addColumn("percentage", array(
				"header" => Mage::helper("cform")->__("Vat Percentage"),
				"index" => "percentage",
				));
						$this->addColumn('status', array(
						'header' => Mage::helper('cform')->__('Status'),
						'index' => 'status',
						'type' => 'options',
						'options'=>Neo_Cform_Block_Adminhtml_Cst_Grid::getOptionArray5(),				
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
			$this->setMassactionIdField('neocform_id');
			$this->getMassactionBlock()->setFormFieldName('neocform_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_cst', array(
					 'label'=> Mage::helper('cform')->__('Remove Cst'),
					 'url'  => $this->getUrl('*/adminhtml_cst/massRemove'),
					 'confirm' => Mage::helper('cform')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray0()
		{
			$data_array=array(); 
			$statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter('IN')->load()->toOptionArray();
			foreach($statearray as $states){
				$data_array[$states['value']] = $states['label'];
			}
			return($data_array);
		}

		static public function getValueArray0()
		{
			$data_array=array();
			foreach(Neo_Cform_Block_Adminhtml_Cst_Grid::getOptionArray0() as $k=>$v){
				$data_array[]=array('value'=>$k,'label'=>$v);		
			}
			return($data_array);
		}

		static public function getOptionArray3()
		{
			$data_array = array(); 
			$categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('id')->addAttributeToSelect('name')->addAttributeToSelect('is_active');
			foreach($categories as $category)
			{
				if ($category->getIsActive()) { // Only pull Active categories
					$data_array[$category->getId()] = $category->getName();
				}
			}
			return($data_array);
		}

		static public function getValueArray3()
		{
			$data_array=array();
			foreach(Neo_Cform_Block_Adminhtml_Cst_Grid::getOptionArray3() as $k=>$v){
				$data_array[]=array('value'=>$k,'label'=>$v);		
			}
			return($data_array);
		}
		
		static public function getOptionArray5()
		{
            $data_array=array(); 
			$data_array[0]='Active';
			$data_array[1]='Inactive';
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(Neo_Cform_Block_Adminhtml_Cst_Grid::getOptionArray5() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}