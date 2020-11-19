<?php

class Neo_Offers_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("offers/offers")->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers  Manager"),Mage::helper("adminhtml")->__("Offers Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Offers"));
			    $this->_title($this->__("Manager Offers"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Offers"));
				$this->_title($this->__("Offers"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("offers/offers")->load($id);
				if ($model->getId()) {
					Mage::register("offers_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("offers/offers");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Manager"), Mage::helper("adminhtml")->__("Offers Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Description"), Mage::helper("adminhtml")->__("Offers Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("offers/adminhtml_offers_edit"))->_addLeft($this->getLayout()->createBlock("offers/adminhtml_offers_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("offers")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Offers"));
		$this->_title($this->__("Offers"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("offers/offers")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("offers_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("offers/offers");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Manager"), Mage::helper("adminhtml")->__("Offers Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Offers Description"), Mage::helper("adminhtml")->__("Offers Description"));


		$this->_addContent($this->getLayout()->createBlock("offers/adminhtml_offers_edit"))->_addLeft($this->getLayout()->createBlock("offers/adminhtml_offers_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("offers/offers")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Offers was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setOffersData(false);
						
						// start of custom coding
						$offer_name = $post_data['offer'];
						$offer_id = $model->getId();
						$resource = Mage::getSingleton('core/resource');
						$write = $resource->getConnection('core_write');
						$read = $resource->getConnection('core_read');
						$relationTable = 'offer_type_attribute_relation';
						$query = 'SELECT * FROM ' . $relationTable .' where offer_id ='.$offer_id;
						$results = $read->fetchAll($query);
						$row_nums = count($results);
						
						if($row_nums == 0)
						{
							$attribute_model = Mage::getModel('eav/entity_attribute');
							$attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
							$attribute_code = $attribute_model->getIdByCode('catalog_product','offers');
							$attribute = $attribute_model->load($attribute_code);
							$attribute_table = $attribute_options_model->setAttribute($attribute);
							$options = $attribute_options_model->getAllOptions(false);
							$value['option'] = array($offer_name,$offer_name,$offer_name);
							$result = array('value' => $value);
							$attribute->setData('option',$result);
							$attribute->save();
							$query = 'INSERT into '. $relationTable.' (offer_id,offer_name,attribute_id) values ('.$offer_id.',"'.$offer_name.'",'.$attribute->getAttributeId().') ';
							$write->query($query);
						}
						else
						{
							$attribute_name = $results[0]['offer_name'];
							$attribute_id = $results[0]['attribute_id'];
							$query ='SELECT * from  eav_attribute_option eo inner join eav_attribute_option_value eav on  eo.option_id = eav.option_id where eav.value="'.$attribute_name.'"';
							$resultsAttrs = $read->fetchAll($query);
							foreach($resultsAttrs as $resultsAttr)
							{
								$option_id  = $resultsAttr['option_id']; 
								$query = 'Update eav_attribute_option_value set value ="'.$offer_name.'" where option_id = '.$option_id;
								$write->query($query);
							}
							$query = 'UPDATE  '. $relationTable.'  set offer_name = "'.$offer_name.'"  where offer_id = '.$offer_id;
							$write->query($query);
						}

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setOffersData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("offers/offers");
						$offer_id = $this->getRequest()->getParam("id");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						
						$resource = Mage::getSingleton('core/resource');
						$write = $resource->getConnection('core_write');
						$read = $resource->getConnection('core_read');
						$relationTable = 'offer_type_attribute_relation';
						$query = 'SELECT * FROM ' . $relationTable .' where offer_id ='.$offer_id;
						$results = $read->fetchAll($query);
						$attribute_name = $results[0]['offer_name'];
						$query ='SELECT eo.option_id from  eav_attribute_option eo inner join eav_attribute_option_value eav on  eo.option_id = eav.option_id where eav.value="'.$attribute_name.'"';
						$resultsAttrs = $read->fetchAll($query);
						$option_id = $resultsAttrs[0][option_id];
						$options['delete'][$option_id] = true; 
						$options['value'][$option_id] = true;
						$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
						$setup->addAttributeOption($options);
						$query = 'DELETE from '.$relationTable.' where offer_id = '.$offer_id;
						$write->query($query);
						
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('cashback_offers_ids', array());
				foreach ($ids as $id) {
				$model = Mage::getModel("offers/offers");
					  $model->setId($id)->delete();
					  
					  $offer_id = $id;
					  $resource = Mage::getSingleton('core/resource');
					  $write = $resource->getConnection('core_write');
				          $read = $resource->getConnection('core_read');
				          $relationTable = 'offer_type_attribute_relation';
				          $query = 'SELECT * FROM ' . $relationTable .' where offer_id ='.$offer_id;
					  $results = $read->fetchAll($query);
					  $attribute_name = $results[0]['offer_name'];
					  $query ='SELECT eo.option_id from  eav_attribute_option eo inner join eav_attribute_option_value eav on  eo.option_id = eav.option_id where eav.value="'.$attribute_name.'"';
					  $resultsAttrs = $read->fetchAll($query);
					  $option_id = $resultsAttrs[0][option_id];
					  $options['delete'][$option_id] = true;
					  $options['value'][$option_id] = true;
					  $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
					  $setup->addAttributeOption($options);
					  $query = 'DELETE from '.$relationTable.' where offer_id = '.$offer_id;
					  $write->query($query);
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'offers.csv';
			$grid       = $this->getLayout()->createBlock('offers/adminhtml_offers_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'offers.xml';
			$grid       = $this->getLayout()->createBlock('offers/adminhtml_offers_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
