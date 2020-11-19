<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pricerange
 *
 * @author Sonia Jain
 */
class Hashtag_DetailPage_Model_System_Config_Source_Pricerange {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {

        $categories = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addIsActiveFilter()->addAttributeToFilter("level", array("eq" => 2));
        $data = $categories->getData();
        $datas = array();
        for ($i = 0; $i < count($data); $i++) { /** $productAttr Mage_Catalog_Model_Resource_Eav_Attribute */
            $category_model = Mage::getModel("catalog/category")->load($data[$i]["entity_id"]);
            $datas[$i]['value'] = $data[$i]["entity_id"];
            $datas[$i]['label'] = $category_model->getData("name");
        }
        return $datas;

//        return array(
//            array('value' => 2000, 'label'=>Mage::helper('adminhtml')->__('2000')),
//            array('value' => 5000, 'label'=>Mage::helper('adminhtml')->__('5000')),
//            array('value' => 10000, 'label'=>Mage::helper('adminhtml')->__('10000')),
        // );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        $categories = Mage::getModel('catalog/category')
                        ->getCollection()
                        ->addAttributeToSelect('*')
                        ->addIsActiveFilter()->addAttributeToFilter("level", array("eq" => 2));
        $data = $categories->getData();
        for ($i = 0; $i < count($data); $i++) { /** $productAttr Mage_Catalog_Model_Resource_Eav_Attribute */
            $category_model = Mage::getModel("catalog/category")->load($data[$i]["entity_id"]);
            $datas[$i]['value'] = $data[$i]["entity_id"];
            $datas[$data[$i]["entity_id"]] = $category_model->getData("name");
        }
        return $datas;

//        return array(
//            2000 => Mage::helper('adminhtml')->__('2000'),
//            5000 => Mage::helper('adminhtml')->__('5000'),
//            10000 => Mage::helper('adminhtml')->__('10000'),
//    
//        );
    }

}
