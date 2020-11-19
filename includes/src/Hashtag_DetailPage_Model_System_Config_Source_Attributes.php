<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Hashtag_DetailPage_Model_System_Config_Source_Attributes {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection');
        $data = array();
        $i = 0;
        foreach ($productAttrs as $productAttr) {
            /** $productAttr Mage_Catalog_Model_Resource_Eav_Attribute */
            $data[$i]['value'] = $productAttr->getAttributeCode();
            $data[$i]['label'] = $productAttr->getFrontendLabel();
            $i++;
        }
        return $data;

//        return array(
//            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Yes')),
//            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('No')),
//        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        $data = array();
        $i = 0;
        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection');
        foreach ($productAttrs as $productAttr) {
            $data[$productAttr->getAttributeCode()] = $productAttr->getFrontendLabel();
        }

//        return array(
//            0 => Mage::helper('adminhtml')->__('No'),
//            1 => Mage::helper('adminhtml')->__('Yes'),
//        );

        return $data;
    }

}