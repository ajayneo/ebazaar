<?php
/**
 * Customy
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Customy EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.customy.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@customy.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.customy.com/ for more information
 * or send an email to sales@customy.com
 *
 * @copyright  Copyright (c) 2011 Triple Dev Studio (http://www.customy.com/)
 * @license    http://www.customy.com/LICENSE-1.0.html
 */
 class Customy_ProductsByDate_Model_Rewrite_Adminhtml_Model_System_Config_Source_Catalog_ListSort extends Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort
{
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!Mage::getStoreConfig("productsbydate/options/active")){
            return parent::toOptionArray();
        }
        
        $options = array();
        $options[] = array(
            'label' => Mage::helper('productsbydate')->__('Position'),
            'value' => 'position'
        );
        $options[] = array(
            'label' => Mage::helper('productsbydate')->__('Newest First'),
            'value' => 'entity_id'
        );
        
        
        foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
            $options[] = array(
                'label' => Mage::helper('catalog')->__($attribute['frontend_label']),
                'value' => $attribute['attribute_code']
            );
        }
        return $options;
    }

}
