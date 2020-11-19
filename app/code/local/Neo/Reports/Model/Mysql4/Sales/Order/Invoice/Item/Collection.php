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
 * Flat sales order collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Neo_Reports_Model_Mysql4_Sales_Order_Invoice_Item_Collection extends Mage_Sales_Model_Mysql4_Order_Invoice_Item_Collection
{	

	/**
     * Add Product Details
     *
     * @return Neo_Reports_Model_Mysql4_Sales_Order_Invoice_Item_Collection
     */
    public function addProductDetails(){        
        $productTable = $this->getTable('catalog/product');
        $orderItemTable = $this->getTable('sales/order_item');
        $this->getSelect()
                ->join(array('product_table' => $productTable), "product_table.entity_id = main_table.product_id", array(
                    'sku' => 'product_table.sku',
                ));
		$this->getSelect()
                ->join(array('order_item_table' => $orderItemTable), "order_item_table.item_id = main_table.order_item_id", array(
                    'itemtag' => 'main_table.itemtag',
                ));
		$this->getSelect()->group('main_table.entity_id');
        return $this;
	}
}
