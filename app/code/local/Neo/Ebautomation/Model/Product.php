<?php
class Neo_Ebautomation_Model_Product extends Mage_Core_Model_Abstract
{
	
	/*
	* @Author : Sonali Kosrabe
	* @Date : 3-nov-2017
	* @Purpose : To lock product attribute
	*/

	public function lockProductAttribute($observer){
		$event = $observer->getEvent();
	    $product = $event->getProduct();
	    $product->lockAttribute('mapped_status');
	}

	/*
	* @Author : Sonali Kosrabe
	* @Date : 02-nov-2017
	* @Purpose : To Map Product in Navision
	*/

	public function notMappedProducts(){
		Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);

		$products = Mage::getModel('catalog/product')->getCollection()
					->addFieldToFilter('attribute_set_id', array('nin'=>array('48')))
					->addFieldToFilter('mapped_status',array('nin'=>array('1')));
		echo "<pre>";
		echo $products->count();
		print_r($products->getColumnValues('entity_id'));
		//die;
		//
		//Mage::getModel('ebautomation/product')->allProductMapping();
		foreach ($products->getColumnValues('entity_id') as $productId) {
			echo "<br>Product ID: ".$productId;

			$product = Mage::getModel('catalog/product')->load($productId);
			echo $product->getMappedStatus();
			Mage::getModel('ebautomation/product')->mapProductInNavision($product);
		}
	}

	

	public function productImport(Varien_Event_Observer $observer){

		if(!Mage::getStoreConfig('ebautomation/ebautomation_group/ebautomation_select')){
			Mage::getSingleton("adminhtml/session")->addError("Navision automation is disabled.");
			return;
		}

		$event = $observer->getEvent();
		$product = $event->getProduct();
		Mage::getModel('ebautomation/product')->mapProductInNavision($product);
	}

	public function remove_bs($Str) {  
		$StrArr = str_split($Str); $NewStr = '';
		foreach ($StrArr as $Char) {    
			$CharNo = ord($Char);
			if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
			if ($CharNo > 31 && $CharNo < 127) {
			  $NewStr .= $Char;    
			}
		}  
		return $NewStr;
		//return filter_input(INPUT_GET,$Str,FILTER_SANITIZE_STRING,!FILTER_FLAG_STRIP_LOW);
		//return mb_convert_encoding($Str, 'UTF-8', 'UTF-8');

	}

	/*function remove_bs($string){
		   $string = html_entity_decode($string);
    	return $string;
	}
*/
	/*function remove_bs($string) {
	    return preg_replace(
	            array('#[\\s-]+#', '#[^A-Za-z0-9\. -]+#'),
	            array('-', ''),
	              urldecode($string)
	            );
	    //return htmlspecialchars($string);
	    //return $answer=iconv("UTF-8", "ISO-8859-1//IGNORE", $string);
	    //return filter_var ( $string, FILTER_SANITIZE_STRING);

	}*/



	public function mapProductInNavision($product){

	
		$datetime = gmdate("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); 
		$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
		$attributeSetModel->load($product->getAttributeSetId());
		$attributeSetName  = $attributeSetModel->getAttributeSetName();
		$storeId = Mage::app()->getStore()->getStoreId();

		if($attributeSetName == 'Sell Your Gadget' || $product->getAttributeSetId() == 48){
			$attributesData = array('mapped_details'=>$product->getmapped_details()."\r\n #".$datetime.': Sell your gadget product will not be mapped in Navision.','mapped_status'=>'0');
			Mage::getSingleton('catalog/product_action')
        		->updateAttributes(array($product->getId()), $attributesData, $storeId);
        		return;
		}
		
		if($product->getSku() == NULL){
			$attributesData = array('mapped_details'=>$datetime.': SKU should not be empty.','mapped_status'=>'0','price' => 0);
			try{
				Mage::getSingleton('catalog/product_action')
        		->updateAttributes(array($product->getId()), $attributesData, $storeId);
        		return;
        	}catch(Exception $e){
        		$attributesData = array('mapped_details'=>$e->getMessage());
            	Mage::getSingleton('catalog/product_action')
            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
            	return;
        	}
			
		}

		if($product->getMappedStatus()){
			$this->updateProductImport($product);
			return;
		}


		
		$productsData = array();
		
			$_cat = array();
			$categoryName = array(); $navCategoryCode = array();
			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			$hsnCode = Mage::helper('indiagst')->getHsnBySku($product->getSku());
			$category = $categoryName[0];
			$subcategory = $categoryName[1];
			$website_ids = implode(',',$product->getWebsiteIds());
			$taxClassId = $product->getTaxClassId();
		    $taxClass = Mage::getModel('tax/class')->load($taxClassId);
		    $taxClassName = $taxClass->getClassName();
		    if($taxClassName != 'None'){
		    	$taxClassName = $taxClassName."%";
		    }
		    

			$productsData = array('sku'=>$product->getSku(),
								'type'=>$product->getTypeId(),
								'category'=>strtoupper($product->getAttributeText('product_category_type')), 
								'subcategory'=>strtoupper($product->getAttributeText('brands')),
								'name'=>trim(htmlentities(substr(trim(str_replace('&nbsp;', ' ', $product->getName())),0,50))),
								'short_description'=> trim(htmlentities(substr(trim(str_replace('&nbsp;', ' ', $product->getName())),0,250))),
								'hsn_code'=>$hsnCode,
								'status'=>$product->getStatus()==1?1:0,
								'brand'=>strtoupper($product->getAttributeText('brands')),
								'tax_class_id'=>$taxClassName,
								'base_unit_of_measure'=>'PCS',
								'inventory_posting_group'=>'TRADING',
								'costing_method'=>'FIFO',
								'gen_prod_posting_group'=>'TRADING',
								'sales_unit_of_measure'=>'PCS',
								'purch_unit_of_measure'=>'PCS'
							);
		$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_product_url');
		
		try{

			$productName = $this->remove_bs($productsData['name']);
		
			$action = 'Create';
			$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ite="urn:microsoft-dynamics-schemas/page/itemcard">
					    <x:Header/>
					    <x:Body>
					        <ite:Create>
					            <ite:ItemCard>
					            	<ite:No>'.$productsData['sku'].'</ite:No>
					                <ite:Magento_Item_Code>'.$productsData['sku'].'</ite:Magento_Item_Code>
					                <ite:HSN_Code>'.$productsData['hsn_code'].'</ite:HSN_Code>
					                <ite:Description>'.$productName.'</ite:Description>
					                <ite:Search_Description>'.$productName.'</ite:Search_Description>
					                <ite:Item_Description>'.$productsData['short_description'].'</ite:Item_Description>
					                <ite:Item_Category_Code>'.$productsData['category'].'</ite:Item_Category_Code>
					                <ite:Magento_Category_2>'.$productsData['subcategory'].'</ite:Magento_Category_2>
					                <ite:Tax_Group_Code>'.$productsData['tax_class_id'].'</ite:Tax_Group_Code>
					                <ite:Brand>'.$productsData['brand'].'</ite:Brand>
					                <ite:Enable>'.$productsData['status'].'</ite:Enable>
					                <ite:Base_Unit_of_Measure>'.$productsData['base_unit_of_measure'].'</ite:Base_Unit_of_Measure>
					                <ite:Inventory_Posting_Group>'.$productsData['inventory_posting_group'].'</ite:Inventory_Posting_Group>
					                <ite:Costing_Method>'.$productsData['costing_method'].'</ite:Costing_Method>
					                <ite:Gen_Prod_Posting_Group>'.$productsData['gen_prod_posting_group'].'</ite:Gen_Prod_Posting_Group>
					                <ite:Sales_Unit_of_Measure>'.$productsData['sales_unit_of_measure'].'</ite:Sales_Unit_of_Measure>
					            </ite:ItemCard>
					        </ite:Create>
					    </x:Body>
					</x:Envelope>';


					//<ite:Blocked>'.$productsData['status'].'</ite:Blocked>
					                

			//echo "<pre>".$params;die;
			$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
			$datetime = gmdate("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); 
			if($result['sBody']->sFault){
				$faultstring = $result['sBody']->sFault->faultstring;
				$response = $product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": ".$faultstring;//die;
				
					try{
						if($faultstring == 'The record already exists.'){
							$attributesData = array('mapped_details'=>$response,'mapped_status'=>'1');
						}else{
							$attributesData = array('mapped_details'=>$response,'mapped_status'=>'0');
						}
						Mage::getSingleton('catalog/product_action')
	            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
	            	}catch(Exception $e){
	            		$attributesData = array('mapped_details'=>$response.' '.$e->getMessage(),'mapped_status'=>'0');
	            		Mage::getSingleton('catalog/product_action')
	            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
	            	}
				
				
			}else{

				$key = $result['SoapBody']->Create_Result->ItemCard->Key;
				$response = $product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": Mapped with key : ".$key;
				if($key == '' || $key == null){
					$mapped_status = '0';
				}else{
					$mapped_status = '1';
				}
				$attributesData = array('mapped_details'=>$response,'mapped_status'=>$mapped_status);
				try{
					Mage::getSingleton('catalog/product_action')
            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
            	}catch(Exception $e){
            		$response = $product->getmapped_details()."\r\n #".$product->getSku()." #".$datetime.": ".$e->getMessage();
            		$attributesData = array('mapped_details'=>$response);
	            	Mage::getSingleton('catalog/product_action')
	            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
            	}
			}

			
		}catch(Exception $e){
			$response = $product->getmapped_details()."\r\n #".$product->getSku()." #".$datetime.": ".$e->getMessage();//die;
			$attributesData = array('mapped_details'=>$response,'mapped_status'=>'0');
	        Mage::getSingleton('catalog/product_action')
	        ->updateAttributes(array($product->getId()), $attributesData, $storeId);
		}





	 
	}

	public function updateProductImport($product){
		$productsData = array();
		
			$_cat = array();
			$categoryName = array(); $navCategoryCode = array();
			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			$hsnCode = Mage::helper('indiagst')->getHsnBySku($product->getSku());
			$category = $categoryName[0];
			$subcategory = $categoryName[1];
			$website_ids = implode(',',$product->getWebsiteIds());
			$taxClassId = $product->getTaxClassId();
		    $taxClass = Mage::getModel('tax/class')->load($taxClassId);
		    $taxClassName = $taxClass->getClassName();
		    if($taxClassName != 'None'){
		    	$taxClassName = $taxClassName."%";
		    }
		    

			$productsData = array('sku'=>$product->getSku(),
								'type'=>$product->getTypeId(),
								'category'=>strtoupper($product->getAttributeText('product_category_type')), 
								'subcategory'=>strtoupper($product->getAttributeText('brands')),
								'name'=>trim(htmlentities(substr(trim(str_replace('&nbsp;', ' ', $product->getName())),0,50))),
								'short_description'=> trim(htmlentities(substr(trim(str_replace('&nbsp;', ' ', $product->getName())),0,250))),
								'hsn_code'=>$hsnCode,
								'status'=>$product->getStatus()==1?1:0,
								'brand'=>strtoupper($product->getAttributeText('brands')),
								'tax_class_id'=>$taxClassName,
								'base_unit_of_measure'=>'PCS',
								'inventory_posting_group'=>'TRADING',
								'costing_method'=>'FIFO',
								'gen_prod_posting_group'=>'TRADING',
								'sales_unit_of_measure'=>'PCS',
								'purch_unit_of_measure'=>'PCS'
							);
		$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_product_url');
		
		try{
		
			
			$action = 'Read';
			$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ite="urn:microsoft-dynamics-schemas/page/itemcard">
					    <x:Header/>
					    <x:Body>
					        <ite:Read>
					            <ite:No>'.$productsData['sku'].'</ite:No>
					        </ite:Read>
					    </x:Body>
					</x:Envelope>';


					//<ite:Blocked>'.$productsData['status'].'</ite:Blocked>
					                

			//echo "<pre>".$params;die;
			$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
			//echo "<pre>";print_r($result);die;
			$datetime = gmdate("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); 
			if($result['sBody']->sFault){
				$faultstring = $result['sBody']->sFault->faultstring;
				$response = $product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": ".$faultstring;//die;
				
					try{
						if($faultstring == 'The record already exists.'){
							$attributesData = array('mapped_details'=>$response);
						}else{
							$attributesData = array('mapped_details'=>$response);
						}
						Mage::getSingleton('catalog/product_action')
	            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
	            	}catch(Exception $e){

	            		$attributesData = array('mapped_details'=>$response.' '.$e->getMessage());
	            		Mage::getSingleton('catalog/product_action')
	            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
	            	}
				
				
			}else{



				$key = $result['SoapBody']->Read_Result->ItemCard->Key;
				if(isset($key) && $key != NULL){

					$productName = $this->remove_bs($productsData['name']);

					$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ite="urn:microsoft-dynamics-schemas/page/itemcard">
						    <x:Header/>
						    <x:Body>
						        <ite:Update>
						            <ite:ItemCard>
						            	<ite:Key>'.$key.'</ite:Key>
						            	<ite:No>'.$productsData['sku'].'</ite:No>
						                <ite:Magento_Item_Code>'.$productsData['sku'].'</ite:Magento_Item_Code>
						                <ite:HSN_Code>'.$productsData['hsn_code'].'</ite:HSN_Code>
						                <ite:Description>'.$productName.'</ite:Description>
						                <ite:Search_Description>'.$productName.'</ite:Search_Description>
						                <ite:Item_Description>'.$productsData['short_description'].'</ite:Item_Description>
						                <ite:Item_Category_Code>'.$productsData['category'].'</ite:Item_Category_Code>
						                <ite:Magento_Category_2>'.$productsData['subcategory'].'</ite:Magento_Category_2>
						                <ite:Tax_Group_Code>'.$productsData['tax_class_id'].'</ite:Tax_Group_Code>
						                <ite:Brand>'.$productsData['brand'].'</ite:Brand>
						                <ite:Enable>'.$productsData['status'].'</ite:Enable>
						                <ite:Base_Unit_of_Measure>'.$productsData['base_unit_of_measure'].'</ite:Base_Unit_of_Measure>
						                <ite:Inventory_Posting_Group>'.$productsData['inventory_posting_group'].'</ite:Inventory_Posting_Group>
						                <ite:Costing_Method>'.$productsData['costing_method'].'</ite:Costing_Method>
						                <ite:Gen_Prod_Posting_Group>'.$productsData['gen_prod_posting_group'].'</ite:Gen_Prod_Posting_Group>
						                <ite:Sales_Unit_of_Measure>'.$productsData['sales_unit_of_measure'].'</ite:Sales_Unit_of_Measure>
						            </ite:ItemCard>
						        </ite:Update>
						    </x:Body>
						</x:Envelope>';
				}
				$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
				//echo "<pre>"; print_r($result);die;
				if($result['sBody']->sFault){
					$faultstring = $result['sBody']->sFault->faultstring;
					$response = $product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": ".$faultstring;//die;
					
						try{
							if($faultstring == 'The record already exists.'){
								$attributesData = array('mapped_details'=>$response);
							}else{
								$attributesData = array('mapped_details'=>$response);
							}
							Mage::getSingleton('catalog/product_action')
		            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
		            	}catch(Exception $e){
		            		$attributesData = array('mapped_details'=>$response.' '.$e->getMessage());
		            		Mage::getSingleton('catalog/product_action')
		            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
		            	}
					
					
				}else{
					if($key == '' || $key == null){
						$mapped_status = '0';
						$response = $product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": Error in mapping and response getting null";
					}else{
						$mapped_status = '1';
						$response = $product->getmapped_details()."\r\n#".$product->getSku()." #".$datetime.": Updated with key : ".$key;
					}
					
					$attributesData = array('mapped_details'=>$response,'mapped_status'=>$mapped_status);
					try{
						Mage::getSingleton('catalog/product_action')
	            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
	            	}catch(Exception $e){
	            		$response = $product->getmapped_details()."\r\n #".$product->getSku()." #".$datetime.": ".$e->getMessage();
	            		$attributesData = array('mapped_details'=>$response);
		            	Mage::getSingleton('catalog/product_action')
		            		->updateAttributes(array($product->getId()), $attributesData, $storeId);
	            	}
				}
			}

			
		}catch(Exception $e){
			$response = $product->getmapped_details()."\r\n #".$product->getSku()." #".$datetime.": ".$e->getMessage();//die;
			$attributesData = array('mapped_details'=>$response,'mapped_status'=>'0');
	        Mage::getSingleton('catalog/product_action')
	        ->updateAttributes(array($product->getId()), $attributesData, $storeId);
		}


	 
	}


	


}
?>