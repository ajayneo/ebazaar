<?php

class Neo_Operations_Adminhtml_ProductsaleController extends Mage_Adminhtml_Controller_Action
{
		protected function _isAllowed()
		{
		//return Mage::getSingleton('admin/session')->isAllowed('operations/productsale');
			return true;
		}

		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("operations/productsale")->_addBreadcrumb(Mage::helper("adminhtml")->__("Productsale  Manager"),Mage::helper("adminhtml")->__("Productsale Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Operations"));
			    $this->_title($this->__("Manager Productsale"));

				$this->_initAction();
				$this->renderLayout();
		}
		

		public function getProductSaleReportAction()
		{
			$posts = $this->getRequest()->getParams();
			
			try {
				ini_set("memory_limit",-1);
				$now = Mage::getModel('core/date')->timestamp(time());
				$to = date("Y-m-d 23:59:59",strtotime($posts['to'])); //$from
				$from = date("Y-m-d 00:00:00",strtotime($posts['from']));


				$category_ids = $posts['category_id'];
				$sub_category_ids = $posts['sub_category_id'];

				$categories = array(); //$category_ids;
				if($category_ids!= ''){
					$categories[] = $category_ids;
				}
				if($sub_category_id!= ''){
					$categories[] = $sub_category_id;
				}
				$_category = Mage::getModel('catalog/category')->load($category_ids); 
				$categoryName = $_category->getName(); 

				$_subCategory = Mage::getModel('catalog/category')->load($sub_category_ids); 
				$subCategoryName = $_subCategory->getName(); 
				$categoriesName = $categoryName.','.$subCategoryName;


				$collection = Mage::getModel('sales/order_item')->getCollection();
				$collection->getSelect()->joinLeft(array('ccp' => 'catalog_category_product'), 'ccp.product_id = main_table.product_id', array('ccp.category_id'));



				/*$collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.order_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region'));*/

				 if(!empty($posts['group_by']) && $posts['group_by'] == 'state'){

                	$collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.order_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.region'));
                	$collection->getSelect()->group('sfoa.region');

            	}elseif(!empty($posts['group_by']) && $posts['group_by'] == 'asm'){

            		$attributeId = Mage::getModel('customer/customer')->getResource()->getAttribute('asm_map')->getId();

            		$collection->getSelect()->joinLeft(array('sfo' => 'sales_flat_order'), 'main_table.order_id = sfo.entity_id', array('sfo.customer_id'));
            		$collection->getSelect()->joinLeft(array('ce' => 'customer_entity'), 'sfo.customer_id = ce.entity_id', array('ce.entity_id'));

            		$collection->getSelect()->joinLeft(array('cei' => 'customer_entity_int'), 'ce.entity_id = cei.entity_id AND cei.attribute_id = ' . $attributeId, array('asm_map' => 'cei.value'));
					$collection->getSelect()->group('cei.value');
            	}

            	if($categories != NULL){
            		$collection->addAttributeToFilter('ccp.category_id', array('in' => array('finset' => $categories)));
				}
            	//
            	//$collection->addAttributeToFilter('ccp.category_id', array('in' => array('finset' => $categories)));
				if(!empty($posts['from']) && !empty($posts['to'])){
					$collection->addAttributeToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
				}

				$collection ->getSelect()->columns('SUM(main_table.qty_ordered) as total_qty, SUM(main_table.base_row_total) as total_sale');
                
                
				/*
				echo $collection->getSelect();
				echo "<pre>";
                print_r($collection->getData());
                die;*/
				
				$states = array();$data = array(); $titles = array();

				if(!empty($posts['group_by']) && $posts['group_by'] == 'state'){
					$titles = array('State','Category','Qty','Sale');
					foreach ($collection as $item) {
						$data[$item->getRegion()]['total_qty'] = $item->getTotalQty();
						$data[$item->getRegion()]['total_sale'] = $item->getTotalSale();
					}
				}elseif(!empty($posts['group_by']) && $posts['group_by'] == 'asm'){
					$titles = array('ASM','Category','Qty','Sale');

					

					foreach ($collection as $item) {
						$customerModel = Mage::getModel('customer/customer')->load($item->getCustomerId());

						$attr = $customerModel->getResource()->getAttribute("asm_map");
						if ($attr->usesSource()) {
						    $asmName = $attr->getSource()->getOptionText($item->getAsmMap());
						}

						$data[$asmName]['total_qty'] = $item->getTotalQty();
						$data[$asmName]['total_sale'] = $item->getTotalSale();
					}
				}

				
				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=ProductSaleReport.csv');
				$export = fopen('php://output', 'w');

				fputcsv($export, $titles); // write the file header with the column names
				
		        foreach ($data as $key => $value) {
		        	
		        	$saleData = array($key,
		        						empty($categories)?'ALL':trim($categoriesName,','),
		        						(int)$value['total_qty'],
		        						Mage::helper('core')->currency($value['total_sale'], true, false)
		        						);
		        	fputcsv($export, $saleData);//Add the fields you added in csv header
		        }
		        fclose($export );
		        return;
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}

		public function getSubcategoriesAction(){
			//echo "sda";
			$sub_category_id = $this->getRequest()->getParam('sub_category_id');
			$category = Mage::getModel('catalog/category')->load($sub_category_id);
			$subcategories = $category->getChildrenCategories();
			//print_r($subcategories->getData());die;
			$options = '';
			if (count($subcategories) > 0){
				$options .="<option value=''>-- All --</option>";
			    foreach($subcategories as $subcategory){
			        $options .="<option value=".$subcategory->getId().">".$subcategory->getName()."</option>";
			    }
			}
			echo $options;
			exit;

		}


		/* 
		@author : Sonali Kosrabe
		@date : 3rd Jan 2018
		@purpose : To get weekday count
		*/
		public function getWeekday($date) {
		    return date('w', strtotime($date));
		}

		/* 
		@author : Sonali Kosrabe
		@date : 3rd Jan 2018
		@purpose : To get per day product sale report
		*/

		public function getPerDayProductSaleReportAction()
		{
			//$posts = $this->getRequest()->getParams();
			
			try {
				ini_set("memory_limit",-1);

				// One day Logic
				$to = date("Y-m-d 23:59:59"); //$from
				$from = date("Y-m-d 00:00:00",strtotime('-1 day'));
				
				$html .= Mage::getModel('operations/productsale')->getCategorySaleReport($to,$from,'Daily');

				$html .= "<br/><br/>";

				///// Weekly Logic
				$today =  date('Y-m-d');
				$weekDay = $this->getWeekday($today);
				if($weekDay == 1){
					$weekStart = 7;
				}else{
					$weekStart = $weekDay - 1;
				}
				
				$weekTo = date('Y-m-d 23:59:59', strtotime('-1 day'));
				$weekFrom = date("Y-m-d 00:00:00",strtotime('-'.$weekStart.' day'));
				
				$html .= Mage::getModel('operations/productsale')->getCategorySaleReport($weekTo,$weekFrom,'Weekly');

				$html .= "<br/><br/>";

				/// Yearly Logic
				$today =  date('Y-m-d');
  				$month = date('m',strtotime($today));
  				$date = date('d',strtotime($today));
  				
  				if($date == '01'){
  					$monthFrom = date('Y-m-d 00:00:00', strtotime("first day of last month"));
  					$monthTo = date('Y-m-d 23:59:59', strtotime("last day of last month"));
  				}else{
  					$monthFrom = date('Y-m-01 00:00:00');
  					$monthTo = date('Y-m-d 23:59:59', strtotime("-1 day"));
  				}
				
				$html .= Mage::getModel('operations/productsale')->getCategorySaleReport($monthTo,$monthFrom,'Monthly');
				
				

				$templateId = 29;
				$emailTemplate = Mage::getModel('core/email_template')->load($templateId);
				$template_variables['html'] = $html;
				$storeId = Mage::app()->getStore()->getId();
				$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
				$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
				$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
				// Set sender information			
				$sender = array('name' => $senderName,'email' => $senderEmail);
				$recepientEmail = array('sonalik2788@gmail.com','mangesh.r@electronicsbazaar.com');
				//$recepientName = 'Sonali Kosrabe';
				$subject = $emailTemplate->getTemplateSubject();
				$z_mail = new Zend_Mail('utf-8');
		    	$z_mail->setBodyHtml($processedTemplate)
		        ->setSubject($subject)
		        ->setFrom($senderEmail, $senderName);
		        $z_mail->addTo($recepientEmail);
				try{
					$z_mail->send();
				}catch(Exception $e){
					echo $e->getMessage();
				}

				Mage::getSingleton("adminhtml/session")->addSuccess("Please check Mail");
				
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
}

