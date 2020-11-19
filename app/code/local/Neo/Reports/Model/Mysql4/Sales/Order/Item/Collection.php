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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



/**
 * Flat sales order Item collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Neo_Reports_Model_Mysql4_Sales_Order_Item_Collection extends Mage_Sales_Model_Mysql4_Order_Item_Collection
{	  
	
	/**
     * Add Product Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Collection
     */
    public function addProductDetails(){        
        $productTable = $this->getTable('catalog/product');
        $this->getSelect()
                ->join(array('product_table' => $productTable), "product_table.entity_id = main_table.product_id AND (product_table.type_id='simple' OR product_table.type_id='virtual')", array(
                    'sku' => 'product_table.sku',
                ));
		$productTable = Mage::getResourceSingleton('catalog/product');
        $attreValuecode = $productTable->getAttribute('custom_evalue_code');
		$attreInvoicecode = $productTable->getAttribute('custom_invoice_display');
        $attreValuecodeId = $attreValuecode->getAttributeId();
		$attreInvoicecodeId = $attreInvoicecode->getAttributeId();
        $attreValueTableName = $attreValuecode->getBackend()->getTable();
		
        $this->getSelect()
                ->joinLeft(
						array('product_table_varchar' => $attreValueTableName), 
						"product_table_varchar.entity_id = product_table.entity_id AND product_table.type_id='simple' and product_table_varchar.attribute_id=" . $attreValuecodeId, 
						array('evalue_code' => 'product_table_varchar.value')
						)
				->joinLeft(
						array('custominvoicedisplay' => $attreValueTableName), 
						"custominvoicedisplay.entity_id = product_table.entity_id AND product_table.type_id='simple' and custominvoicedisplay.attribute_id=" . $attreInvoicecodeId, 
						array('custom_invoice_display' => 'custominvoicedisplay.value')
						);
        return $this;
	}
	public function addServicetagDetails(){        
        $productTable = $this->getTable('catalog/product');
        $this->getSelect()
                ->join(array('product_table' => $productTable), "product_table.entity_id = main_table.product_id", array(
                    'sku' => 'product_table.sku',
                ));
		$productTable = Mage::getResourceSingleton('catalog/product');
        $attreValuecode = $productTable->getAttribute('custom_evalue_code');
		$attreInvoicecode = $productTable->getAttribute('custom_invoice_display');
        $attreValuecodeId = $attreValuecode->getAttributeId();
		$attreInvoicecodeId = $attreInvoicecode->getAttributeId();
        $attreValueTableName = $attreValuecode->getBackend()->getTable();
		
        $this->getSelect()
                ->joinLeft(
						array('product_table_varchar' => $attreValueTableName), 
						"product_table_varchar.entity_id = product_table.entity_id and product_table_varchar.attribute_id=" . $attreValuecodeId, 
						array('evalue_code' => 'product_table_varchar.value')
						)
				->joinLeft(
						array('custominvoicedisplay' => $attreValueTableName), 
						"custominvoicedisplay.entity_id = product_table.entity_id and custominvoicedisplay.attribute_id=" . $attreInvoicecodeId, 
						array('custom_invoice_display' => 'custominvoicedisplay.value')
						);
        return $this;
	}
}
