<?php   

class SSTech_BulkUploadProduct_Block_Index extends Mage_Core_Block_Template
{   
    const XML_PATH_COLUMN_LENGTH     = 'bulkuploadproduct/functionality/column';
    protected function getProductCollection()
    {
        $html = '<datalist id="nameproductauto">';
        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('price','sku','name'))->addAttributeToFilter('type_id', array('eq' => 'simple'));
        foreach($productCollection as $product)
        {
            if($product->isSaleable())
            {
                $html .= '<option value="'.$product->getName().'">';
            }
        }
        return $html.'</datalist>';
    }   

    protected function getColumnLength()
    {
        $columnLength = Mage::getStoreConfig(self::XML_PATH_COLUMN_LENGTH, Mage::app()->getStore()->getStoreId());
        return explode(',',$columnLength);
    }

    protected function getColumnCount()
    {
        $columnLength = Mage::getStoreConfig(self::XML_PATH_COLUMN_LENGTH, Mage::app()->getStore()->getStoreId());
        $array = explode(',',$columnLength);
        $sum = '';
        foreach($array as $data){ 
            $sum = $sum + $data;
        }
        return $sum--;
    }

    protected function getAllActiveProductCollection(){

        $products = $this->getRequest()->getPost('searchproduct');

        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect(array('price','sku','name','image','description','special_price'));
        // checking if product are in stock
        $collection->joinField('qty','cataloginventory/stock_item','qty','product_id=entity_id','{{table}}.stock_id=1','left');
        $collection->addAttributeToFilter('qty', array('gt' => 0));
        $collection->addAttributeToFilter('status', array('eq' => 1)); 
        $collection->addAttributeToFilter('visibility', array('eq' => 4)); 
        if($products){
            $collection->addAttributeToFilter('name', array('like' => '%'.strtolower($products).'%'));
        }
        

        return  $collection;
    }
}