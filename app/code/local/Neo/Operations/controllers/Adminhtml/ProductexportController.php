<?php

class Neo_Operations_Adminhtml_ProductexportController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('operations/productsale');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("operations/productsale")->_addBreadcrumb(Mage::helper("adminhtml")->__("Product Export"),Mage::helper("adminhtml")->__("Product Export"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Operations"));
			    $this->_title($this->__("Product Export"));

				$this->_initAction();
				$this->renderLayout();
		}
		
		/*
		* @author : Sonali Kosrabe
		* @date : 27th Mar 2018
		* @purpose : To get Product Export Reports with specfic fields
		*/
		public function getProductExportReportAction()
		{
			
			try {
				ini_set("memory_limit",-1);
				
				$products = Mage::getResourceModel('catalog/product_collection')//->AddAttributeToSelect('name');
                ->addAttributeToSelect(array('name','price', 'description','status'))
			    ->joinField(
			        'qty',
			        'cataloginventory/stock_item',
			        'qty',
			        'product_id=entity_id',
			        '{{table}}.stock_id=1',
			        'left'
			    );

				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=ProductExport-'.date('dmy').'.csv');
				$export = fopen('php://output', 'w');
				$titles = array('attribute_set', 'sku','category_ids','qty','price','status','name','description');
				fputcsv($export, $titles); // write the file header with the column names
				
		        foreach ($products as $product) {
		        	//print_r($product->getData());
		        	$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
					$attributeSetModel->load($product->getAttributeSetId());
					$attributeSetName  = $attributeSetModel->getAttributeSetName();
		        	
		        	$data = array($attributeSetName,
		        					$product->getSku(),
		        					implode(',',$product->getCategoryIds()),
		        					$product->getQty(),
		        					$product->getPrice(),
		        					$product->getStatus(),
		        					$product->getName(),
		        					$product->getDescription());
		        	
		        
		        	fputcsv($export, $data);//Add the fields you added in csv header
		        }
		        fclose($export );
		        return;
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}

		/*
		* @author : Sonali Kosrabe
		* @date : 27th Mar 2018
		* @purpose : To get Product Stock Reports
		*/
		public function getProductStockReportAction()
		{
			$postData = $this->getRequest()->getPost();
			
			try {
				ini_set("memory_limit",-1);
				
				$collections = Mage::getModel('bubble_stockmovements/stock_movement')->getCollection();
				$collections->getSelect()->joinLeft(array('csi' => 'cataloginventory_stock_item'), 'main_table.item_id = csi.item_id', array('csi.product_id'));
				$collections->getSelect()->joinLeft(array('cpe' => 'catalog_product_entity'), 'cpe.entity_id = csi.product_id', array('cpe.sku'));
				if($postData['from_date'] != ''){
					$from_date = date('Y-m-d 00:00:00',strtotime($postData['from_date']));
					$collections->addFieldToFilter('main_table.created_at',array('from'=>$from_date));
				}
				if($postData['to_date'] != ''){
					$to_date = date('Y-m-d 23:00:00',strtotime($postData['to_date']));
					$collections->addFieldToFilter('main_table.created_at',array('to'=>$to_date));
				}
				$collections->getSelect()->order('main_table.created_at DESC');//->limit(10);

				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=ProductExport-'.date('dmy').'.csv');
				$export = fopen('php://output', 'w');
				$titles = array('Sku', 'Qty','Reason','Updated At');
				fputcsv($export, $titles); // write the file header with the column names
				
		        foreach ($collections as $collection) {
		        	$data = array($collection->getSku(),
	        					$collection->getQty(),
	        					$collection->getMessage(),
	        					$collection->getCreatedAt());
		        	fputcsv($export, $data);//Add the fields you added in csv header
		        }
		        fclose($export );
		        return;
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}



		/*
		* @author : Sonali Kosrabe
		* @date : 29th Mar 2018
		* @purpose : Report for stock updated from Navision with API
		*/
		public function getNavisionStockUpdateReportAction()
		{
			$postData = $this->getRequest()->getPost();
			try {
				ini_set("memory_limit",-1);
				
				$collections = Mage::getModel('soapapi/inventoryupdates')->getCollection();
				
				$collections->getSelect()->joinLeft(array('csi' => 'cataloginventory_stock_item'), 'main_table.stock_item_id = csi.item_id', array('csi.product_id'));
				$collections->getSelect()->joinLeft(array('cpe' => 'catalog_product_entity'), 'cpe.entity_id = main_table.product_id', array('cpe.sku'));
				if($postData['from_date'] != ''){
					$from_date = date('Y-m-d 00:00:00',strtotime($postData['from_date']));
					$collections->addFieldToFilter('main_table.updated_at',array('from'=>$from_date));
				}
				if($postData['to_date'] != ''){
					$to_date = date('Y-m-d 23:00:00',strtotime($postData['to_date']));
					$collections->addFieldToFilter('main_table.updated_at',array('to'=>$to_date));
				}
				$collections->getSelect()->order('main_table.updated_at DESC');//->limit(10);

				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=StockReport_Update_From_Navison-'.date('dmy').'.csv');
				$export = fopen('php://output', 'w');
				$titles = array('Sku', 'Qty Updating from Navision','Reason','Updated At');
				fputcsv($export, $titles); // write the file header with the column names
				
		        foreach ($collections as $collection) {
		        	$data = array($collection->getSku(),
	        					$collection->getQtyFromNavision(),
	        					$collection->getReason(),
	        					$collection->getUpdatedAt());
		        	fputcsv($export, $data);//Add the fields you added in csv header
		        }
		        fclose($export );
		        return;
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}


		/*
		* @author : Sonali Kosrabe
		* @date : 22th Mar 2018
		* @purpose : To update product qty and saving logs in stock movement.
		*/
		public function updateProductQtyAction(){
			$file = $_FILES;
	    	// ini_set('memory_limit', '-1');
			
	    	if (isset($_FILES['productFiles']['name']) && $_FILES['productFiles']['name'] != '') {
			    try {  
			    	$uploader = new Varien_File_Uploader('productFiles');
			        $uploader->setAllowedExtensions(array('csv'));
			        $uploader->setAllowRenameFiles(false);
			        $uploader->setFilesDispersion(false);
			        $path = Mage::getBaseDir('media') . DS.'products'. DS;
			        // $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
			        $fileName = $_FILES['productFiles']['name'];
			        $filePath = $path.$fileName;
			        $uploader->save($path, $fileName);

			        $file_handle = fopen($filePath, 'r');
			       	while (!feof($file_handle) ) {
				 		$data[] = fgetcsv($file_handle, 1024);
				  	}
					fclose($file_handle);

					$i = 1;
					foreach ($data as $key => $value) {
						if($i == 1) { $i++; continue;}
						if(trim($value[0]) == NULL){ continue; }
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($value[0]));
						// echo $product->getId();die;
						if($product){
							if($product->getId()){
								$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
								$oldQty = (int)$stockItem->getQty();
								if($stockItem->getQty() > 0){
				                	$changedQty = $stockItem->getQty() + (int)$value[1]; 
								}else{
									if((int)$value[1] > 0){
										$changedQty = (int)$value[1];	
									}else{
										//$changedQty = 0;
										Mage::getSingleton("adminhtml/session")->addError(trim($value[0]).' - Product qty issue found.');
										continue;
									}
								}
				                $stockItem->setQty($changedQty);
				                $stockItem->setIsInStock((int)($changedQty > 0));
				                $product->setQtyUpdateReason("Qty changed from ".$oldQty." to ".$changedQty." using Magento's Backend Operations Tool");
				                $product->setQtyUpdateFlag(1);
				                $product->save();
				                $stockItem->save();
				                
							}else{
								Mage::getSingleton("adminhtml/session")->addError(trim($value[0]).' - Product not found.');
							}
						}

						$i++;

					}
					Mage::getSingleton("adminhtml/session")->addSuccess('Products qty updated successfully.');
			    	 
			    } catch (Exception $e) {
			        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			    }
			}else{
				Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
			}
	    	
	    	$this->_redirect("*/*/");
		}

		/*
		* @author : Sonali Kosrabe
		* @date : 22th Mar 2018
		* @purpose : To replace product qty and saving logs in stock movement.
		*/
		public function replaceProductQtyAction(){
			$file = $_FILES;
	    	ini_set('memory_limit', '-1');
			
	    	if (isset($_FILES['productFiles']['name']) && $_FILES['productFiles']['name'] != '') {
			    try {  
			    	$uploader = new Varien_File_Uploader('productFiles');
			        $uploader->setAllowedExtensions(array('csv'));
			        $uploader->setAllowRenameFiles(false);
			        $uploader->setFilesDispersion(false);
			        $path = Mage::getBaseDir('media') . DS.'products'. DS;
			        // $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
			        $fileName = $_FILES['productFiles']['name'];
			        $filePath = $path.$fileName;
			        $uploader->save($path, $fileName);

			        $file_handle = fopen($filePath, 'r');
			       	while (!feof($file_handle) ) {
				 		$data[] = fgetcsv($file_handle, 1024);
				  	}
					fclose($file_handle);

					$i = 1;
					foreach ($data as $key => $value) {
						if($i == 1) { $i++; continue;}
						if(trim($value[0]) == NULL){ continue; }
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($value[0]));
						//echo $product->getId();die;

						if($product->getId()){
							$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
			                $oldQty = (int)$stockItem->getQty();
			                $changedQty = (int)$value[1]; 
			                $stockItem->setQty($changedQty);
			                $stockItem->setIsInStock((int)($changedQty > 0));
			                $product->setQtyUpdateReason("Qty replaced from ".$oldQty." to ".$changedQty." using Magento's Backend Operations Tool");
			                $product->setQtyUpdateFlag(1);
			                $product->save();
			                try{
			                	$stockItem->save();
			                	
			            	}catch(Exception $e){
			            		Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			            	}
						}else{
							Mage::getSingleton("adminhtml/session")->addError(trim($value[0]).' - Product not found.');
						}
						$i++;

					}
					Mage::getSingleton("adminhtml/session")->addSuccess('Products qty replaced successfully.');
			    	 
			    } catch (Exception $e) {
			        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			    }
			}else{
				Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
			}
	    	
	    	$this->_redirect("*/*/");
		}


		/*
		* @author : Sonali Kosrabe
		* @date : 04th April 2018
		* @purpose : To map products in Navision.
		*/
		public function mapProductsInNavisionAction(){
			$file = $_FILES;
	    	ini_set('memory_limit', '-1');
			
	    	if (isset($_FILES['productFiles']['name']) && $_FILES['productFiles']['name'] != '') {
			    try {  
			    	$uploader = new Varien_File_Uploader('productFiles');
			        $uploader->setAllowedExtensions(array('csv'));
			        $uploader->setAllowRenameFiles(false);
			        $uploader->setFilesDispersion(false);
			        $path = Mage::getBaseDir('media') . DS.'products'. DS;
			        // $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
			        $fileName = $_FILES['productFiles']['name'];
			        $filePath = $path.$fileName;
			        $uploader->save($path, $fileName);

			        $file_handle = fopen($filePath, 'r');
			       	while (!feof($file_handle) ) {
				 		$data[] = fgetcsv($file_handle, 1024);
				  	}
					fclose($file_handle);

					$i = 1;
					foreach ($data as $key => $value) {
						if($i == 1) { $i++; continue;}
						if(trim($value[0]) == NULL){ continue; }
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($value[0]));
						
						if($product){
							try{
			                	$product->save();
			                	
			            	}catch(Exception $e){
			            		Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			            	}
						}else{
							Mage::getSingleton("adminhtml/session")->addError(trim($value[0]).' - Product not found.');
						}
						$i++;

					}
					Mage::getSingleton("adminhtml/session")->addSuccess('Products updated successfully.');
			    	 
			    } catch (Exception $e) {
			        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			    }
			}else{
				Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
			}
	    	
	    	$this->_redirect("*/*/");
		}

		/*
		* @author : Sonali Kosrabe
		* @date : 04th April 2018
		* @purpose : To update products prices in Magento and create log.
		*/
		public function updateProductPriceAction(){
			$file = $_FILES;
	    	ini_set('memory_limit', '-1');
			
	    	if (isset($_FILES['productFiles']['name']) && $_FILES['productFiles']['name'] != '') {
			    try {  
			    	$uploader = new Varien_File_Uploader('productFiles');
			        $uploader->setAllowedExtensions(array('csv'));
			        $uploader->setAllowRenameFiles(false);
			        $uploader->setFilesDispersion(false);
			        $path = Mage::getBaseDir('media') . DS.'products'. DS;
			        // $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
			        $fileName = $_FILES['productFiles']['name'];
			        $filePath = $path.$fileName;
			        $uploader->save($path, $fileName);

			        $file_handle = fopen($filePath, 'r');
			       	while (!feof($file_handle) ) {
				 		$data[] = fgetcsv($file_handle, 1024);
				  	}
					fclose($file_handle);

					$i = 1;
					foreach ($data as $key => $value) {
						if($i == 1) { $i++; continue;}
						if(trim($value[0]) == NULL){ continue; }
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($value[0]));
						
						if($product){
							try{
								$product->setPrice(trim($value[1]));
			                	$product->save();
			                	
			            	}catch(Exception $e){
			            		Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			            	}
						}else{
							Mage::getSingleton("adminhtml/session")->addError(trim($value[0]).' - Product not found.');
						}
						$i++;

					}
					Mage::getSingleton("adminhtml/session")->addSuccess('Products updated successfully.');
			    	 
			    } catch (Exception $e) {
			        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			    }
			}else{
				Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
			}
	    	
	    	$this->_redirect("*/*/");
		}


		/*
		* @author : Sonali Kosrabe
		* @date : 04th April 2018
		* @purpose : To update products sku in Magento and create log in Automation Information tab in Product edit view.
		*/
		public function updateProductSkuAction(){
			$file = $_FILES;
	    	ini_set('memory_limit', '-1');
			
	    	if (isset($_FILES['productFiles']['name']) && $_FILES['productFiles']['name'] != '') {
			    try {  
			    	$uploader = new Varien_File_Uploader('productFiles');
			        $uploader->setAllowedExtensions(array('csv'));
			        $uploader->setAllowRenameFiles(false);
			        $uploader->setFilesDispersion(false);
			        $path = Mage::getBaseDir('media') . DS.'products'. DS;
			        // $path = Mage::getBaseDir('media') . DS . 'logo' . DS;
			        $fileName = $_FILES['productFiles']['name'];
			        $filePath = $path.$fileName;
			        $uploader->save($path, $fileName);

			        $file_handle = fopen($filePath, 'r');
			       	while (!feof($file_handle) ) {
				 		$data[] = fgetcsv($file_handle, 1024);
				  	}
					fclose($file_handle);

					$i = 1;
					$updatedSku = array();
					$datetime = gmdate("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); 
					foreach ($data as $key => $value) {
						if($i == 1) { $i++; continue;}
						if(trim($value[0]) == NULL){ continue; }
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($value[0]));
						
						if($product){
							try{

								$product->setSku(trim($value[1]));
								$product->setMappedStatus('0');
								$product->setMappedDetails($product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": "."Sku replaced from ".trim($value[0])." to ".trim($value[1]). " by ".Mage::getSingleton('admin/session')->getUser()->getUsername());
			                	$product->save();
			                	$updatedSku[] = trim($value[0]);
			                	
			            	}catch(Exception $e){
			            		Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			            	}
						}else{
							Mage::getSingleton("adminhtml/session")->addError(trim($value[0]).' - Product not found.');
						}
						$i++;

					}
					if(count($updatedSku) > 0){
						Mage::getSingleton("adminhtml/session")->addSuccess(implode(',', $updatedSku).' Products updated successfully.');
					}	
			    	 
			    } catch (Exception $e) {
			        Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			    }
			}else{
				Mage::getSingleton("adminhtml/session")->addError("Please selet File to upload.");
			}
	    	
	    	$this->_redirect("*/*/");
		}
		
}

