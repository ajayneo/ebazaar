<?php
class Neo_CustomBestseller_Block_Bestseller extends Mage_Core_Block_Template
{
    public function getBestsellerProducts()
    {
        $storeId = (int) Mage::app()->getStore()->getId();
 
        // Date
        $date = new Zend_Date();
        // $toDate = $date->setDay(1)->getDate()->get('Y-MM-dd');
        // $fromDate = $date->subMonth(1)->getDate()->get('Y-MM-dd');
 
        $toDate = date("Y-m-d");
        $fromDate = date('Y-m-d', strtotime('-2 month'));

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addPriceData()
            ->addTaxPercents()
            ->addUrlRewrite();
 
        $collection->getSelect()
            // ->joinField('stock_item', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', 'is_in_stock=1')
            ->joinLeft(
                array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
                array('SUM(aggregation.qty_ordered) AS sold_quantity')
            )
            ->group('e.entity_id')
            ->order(array('sold_quantity DESC', 'e.created_at'));
 
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection->joinField('is_in_stock',
        'cataloginventory/stock_item',
        'is_in_stock',
        'product_id=entity_id',
        'is_in_stock=1',
        '{{table}}.stock_id=1',
        'left');

        $product = Mage::getModel('catalog/product')->load(19775);
        $collection->addItem($product); 
        
        $collection->setPageSize(10);
        return $collection;
    }
}