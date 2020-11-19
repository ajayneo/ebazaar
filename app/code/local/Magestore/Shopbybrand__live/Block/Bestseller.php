<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Shopbybrand
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Shopbybrand Block
 * 
 * @category    Magestore
 * @package     Magestore_Shopbybrand
 * @author      Magestore Developer
 */
class Magestore_Shopbybrand_Block_Bestseller extends Mage_Core_Block_Template 
{
    public function getProductBestseller($attributeCodeId){
        $attributeCode = Mage::helper('shopbybrand/brand')->getAttributeCode();
        $numberConfig =  Mage::getStoreConfig('shopbybrand/brand_detail/bestseller_products_number_show', $storeId);
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('visibility', $visibility)
            ->setPageSize($numberConfig);
        $products->addOrderedQty();
        $products->addOrderedQty();
        $products->setOrder('ordered_qty', 'desc');
        $products->addAttributeToFilter($attributeCode,$attributeCodeId);
        return $products;
    }
    
    public function getBrand(){
        if(!$this->hasData('current_brand')){
            $brandId = $this->getRequest()->getParam('id');
            
            $storeId = $this->getStoreId();
            $brand = Mage::getModel('shopbybrand/brand')->setStoreId($storeId)
                    ->load($brandId);
            $this->setData('current_brand', $brand);
        }
        return $this->getData('current_brand');
    }
}

