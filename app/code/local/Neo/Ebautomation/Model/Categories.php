<?php
class Neo_Ebautomation_Model_Categories extends Mage_Core_Model_Abstract
{
	
	/*
	* @Author : Sonali Kosrabe
	* @Date : 02-nov-2017
	* @Purpose : To Map Category in Navision
	*/

	public function categoryImport(Varien_Event_Observer $observer){

		$event = $observer->getEvent();
		$category = $event->getCategory();
		
		// Return if Already Mapped Category in Navision
		if($category->getNavCategoryMappedStatus()){
			return;
		}

		$catData = array('nav_category_code' => $category->getNavCategoryCode(),
						'name' => $category->getName()
					);


		
		$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_item_category_url');
		
		try{
		
			$action = 'Create';
			$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ite="urn:microsoft-dynamics-schemas/page/item_categories">
				    <x:Header/>
				    <x:Body>
				        <ite:Create>
				            <ite:Item_Categories>
				                <ite:Code>'.$catData['nav_category_code'].'</ite:Code>
				                <ite:Description>'.$catData['name'].'</ite:Description>
				                <ite:block>false</ite:block>
				                <ite:Def_Gen_Prod_Posting_Group></ite:Def_Gen_Prod_Posting_Group>
				                <ite:Def_Inventory_Posting_Group></ite:Def_Inventory_Posting_Group>
				                <ite:Def_VAT_Prod_Posting_Group></ite:Def_VAT_Prod_Posting_Group>
				                <ite:Def_Costing_Method>FIFO</ite:Def_Costing_Method>
				            </ite:Item_Categories>
				        </ite:Create>
				    </x:Body>
				</x:Envelope>';


					//<ite:Blocked>'.$productsData['status'].'</ite:Blocked>
					                

			
			$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
			
			//print_r($result);die;
			if($result['sBody']->sFault){
				$faultstring = $result['sBody']->sFault->faultstring;
				$response = $category->getsetnav_category_mapped_details()."\r\n#".Date("Y-m-d H:i:s").": ".$faultstring;
				try{
					$attributesData = array('nav_category_mapped_details'=>$response);
					$storeId = Mage::app()->getStore()->getStoreId();

					$categorySingleton = Mage::getSingleton('catalog/category');
					$categorySingleton->setId($category->getId());
					$categorySingleton->setnav_category_mapped_details($response);
					$categorySingleton->setStoreId($storeId);

					Mage::getModel('catalog/category')->getResource()->saveAttribute($categorySingleton, 'nav_category_mapped_details'); 
				}catch(Exception $e){
					$attributesData = array('nav_category_mapped_details'=>$e->getMessage());
					$categorySingleton = Mage::getSingleton('catalog/category');
					$categorySingleton->setId($category->getId());
					$categorySingleton->setnav_category_mapped_details($e->getMessage());
					$categorySingleton->setStoreId($storeId);
				
					Mage::getModel('catalog/category')->getResource()->saveAttribute($categorySingleton, array('nav_category_mapped_details'));  
				}
			}else{

				$key = $result['SoapBody']->Create_Result->Item_Categories->Key;
				$response = Date("Y-m-d H:i:s").": Mapped with key : ".$key;
				try{
					$attributesData = array('nav_category_mapped_details'=>$response,'nav_category_mapped_status'=>'1');
					$categorySingleton = Mage::getSingleton('catalog/category');
					$categorySingleton->setId($category->getId());
					$categorySingleton->setnav_category_mapped_details($response);
					$categorySingleton->setnav_category_mapped_status(1);
					$categorySingleton->setStoreId($storeId);

					Mage::getModel('catalog/category')->getResource()->saveAttribute($categorySingleton, 'nav_category_mapped_details'); 
					Mage::getModel('catalog/category')->getResource()->saveAttribute($categorySingleton, 'nav_category_mapped_status'); 
            	}catch(Exception $e){
            		$attributesData = array('nav_category_mapped_details'=>$e->getMessage());
					$categorySingleton = Mage::getSingleton('catalog/category');
					$categorySingleton->setId($category->getId());
					$categorySingleton->setnav_category_mapped_details($e->getMessage());
					$categorySingleton->setStoreId($storeId);
				
					Mage::getModel('catalog/category')->getResource()->saveAttribute($categorySingleton, array('nav_category_mapped_details'));  
            	}
			}

			
		}catch(Exception $e){

			$attributesData = array('nav_category_mapped_details'=>$e->getMessage());
			$categorySingleton = Mage::getSingleton('catalog/category');
			$categorySingleton->setId($category->getId());
			$categorySingleton->setnav_category_mapped_details($e->getMessage());
			$categorySingleton->setStoreId($storeId);
		
			Mage::getModel('catalog/category')->getResource()->saveAttribute($categorySingleton, array('nav_category_mapped_details'));   
		}


	 
	}


	


}
?>
		