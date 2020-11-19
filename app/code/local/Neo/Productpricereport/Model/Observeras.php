<?php
class Neo_Productpricereport_Model_Observer
{

	public function adminhtmlCatalogProductSaveAfter($observer)
    {
        $newproduct = $observer->getProduct();
        $oldproduct = Mage::getModel('catalog/product')->load($newproduct->getId());

        if ($newproduct->getId() > 0 && $oldproduct->getData('price') != $newproduct->getData('price')){
            
            $saveData['product_id'] = $newproduct->getId();
            $saveData['to_price'] = $newproduct->getData('price');
            $saveData['from_price'] = $oldproduct->getData('price');
            $admin = Mage::getSingleton('admin/session')->getUser()->getUsername();
            $saveData['changed_by'] = $admin;
            $saveData['name'] = $newproduct->getName();
            
            $date = date('Y-m-d', time()); //Y-m-d HH:MM:ss  2014-09-26 05:51:15
            //$date = date('Y-m-d h:m:s', time()); //Y-m-d HH:MM:ss  2014-09-26 05:51:15

            date_default_timezone_set('Asia/Calcutta');
            $date1 = Mage::getModel('core/date')->gmtDate();
            //echo $date;exit;

            //$endTime = strtotime("+18 minutes", strtotime($date));
            
 
            //$saveData['date'] = date('d/m/Y h:i:s a', $endTime); 

            $saveData['date'] = $date1;

            

            try{

                $model1 = Mage::getModel("productpricereport/productpricereport")->getCollection()->addFieldToFilter('product_id',$saveData['product_id'])->getFirstItem();


            	if($model1->getId()){//echo $model1->getId();exit;
                    $model = Mage::getModel("productpricereport/productpricereport")
                        ->load($model1->getId())
                        ->addData($saveData)
                        ->save();
                }else{
                    $model = Mage::getModel("productpricereport/productpricereport")
                        ->addData($saveData)
                        ->save();
                }
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Product Price Report was successfully created"));

            }catch(Exception $e){
            	return;
            }
        }
    } 
}
