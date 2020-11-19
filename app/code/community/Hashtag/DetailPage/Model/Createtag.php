<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Createtag
 *
 * @author Sonia Jain
 */
class Hashtag_DetailPage_Model_Createtag extends Mage_Core_Model_Abstract {

    private $tagName, $tagModel;

    function setTagName($tagname)
    {
        $this->tagName = $tagname;
    }
    function setTagModel($model) {
        $this->tagModel = $model;
    }

    function getTagModel() {
        return $this->tagModel;
    }

    function getTagName() {
        return $this->tagName;
    }

    function filterTag() {
        $tagModel = $this->getTagModel();
        $tagModel->unsetData()->loadByName($this->getTagName());
        if (!$tagModel->getTagId()) {
            return $this->createTag($tagModel);
        }
        return $tagModel->getTagId();
    }
    
    function filterProduct($attribute_code,$attribute_value,$category_id="4",$type="select")
    {
        $category = Mage::getModel('catalog/category')->load($category_id);
        if($type=="text")
        {
        $model = Mage::getModel("catalog/product")->getCollection()
                ->addAttributeToFilter($attribute_code,array("neq"=>"No"))
                ->addCategoryFilter($category)
                ->load();
        }
        else
        {
         if($attribute_code=="")
         {
         $model = Mage::getModel("catalog/product")->getCollection()
                ->addAttributeToFilter("price",array("neq"=>""))
                ->addCategoryFilter($category)
                 ->load();     
         }
         else
         {
         $model = Mage::getModel("catalog/product")->getCollection()
                ->addAttributeToFilter($attribute_code,array("eq"=>$attribute_value))
                ->addCategoryFilter($category)
                ->load();     
         }
        }
        $product_ids= array();
        $model_data = $model->getData();
        for($i=0;$i<count($model_data);$i++)
        {
            $product_ids[$i]["id"] = $model_data[$i]["entity_id"];
            $product_detail_data = Mage::getModel("catalog/product")->load($product_ids[$i]["id"]);
            $product_price = $product_detail_data->getData("price");
            if($product_price=="" || $product_price == 0)
             $product_ids[$i]["price"] = 1;
            else
             $product_ids[$i]["price"] = $product_price;
        }
       
        return $product_ids;
    }
    
    
    public function filterprice($final_output,$category_id)
    {
        $category = Mage::getModel('catalog/category')->load($category_id);
        if(strlen($final_output)==5)
        {
           $lowerlimit = $final_output - 5000;
           $model = Mage::getModel("catalog/product")->getCollection()->addAttributeToSelect("entity_id")->addAttributeToSelect("price")->addAttributeToFilter("price",array("lteq"=>$final_output))->addAttributeToFilter("price",array("gteq"=>$lowerlimit))->addCategoryFilter($category)->load();
        }
        else if(strlen($final_output)== 4)
        {
           $lowerlimit = $final_output - 1000;
           $model = Mage::getModel("catalog/product")->getCollection()->addAttributeToSelect("entity_id")->addAttributeToSelect("price")->addAttributeToFilter("price",array("lteq"=>$final_output))->addAttributeToFilter("price",array("gteq"=>$lowerlimit))->addCategoryFilter($category)->load();
        }
        else
        {
          $model = Mage::getModel("catalog/product")->getCollection()->addAttributeToSelect("entity_id")->addAttributeToSelect("price")->addAttributeToFilter("price",array("lteq"=>($final_output)))->addCategoryFilter($category)->load();
        }
        $product_ids=array();
        $model_data = $model->getData();
        for($i=0;$i<count($model_data);$i++)
        {
            $product_ids[$i] = $model_data[$i]["entity_id"];
        }
        return $product_ids;
    }

    function createTag($tagModel) {
        $storeId=1;
        try{
        $tagModel->setName($this->getTagName())
                ->setFirstCustomerId(NULL)
                ->setStoreId($storeId)
                ->setStatus($tagModel->getApprovedStatus())
                ->save();
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
        return $tagModel->getTagId();
    }
}