<?php
class Neo_Soapapi_Model_Api extends Mage_Api_Model_Resource_Abstract
{        
	
    public function remove_bs($Str) {  
        $StrArr = str_split($Str); $NewStr = '';
        foreach ($StrArr as $Char) {    
            $CharNo = ord($Char);
            if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
            if ($CharNo > 31 && $CharNo < 127) {
              $NewStr .= $Char;    
            }
        }  
        return $NewStr;
    }

    /* 
    * @author: Sonali Kosrabe
    * @date: 23/01/2018
    * @purpose : To update price from Navision
    */ 
	public function updatePrice($sku,$price,$special_price = '',$special_from_date = '',$special_to_date = '')
    {
        if(!isset($sku) || empty($sku)){
            $this->_fault('sku_not_found');
        }

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if($product){
            $price = str_replace(',', '', $price);
            $special_price = str_replace(',', '', $special_price);
           
            if(isset($price) && (!is_numeric($price) || $price < 0)) { 
                $this->_fault('price_validation');
            }
            if(isset($special_price) && !empty($special_price) && (!is_numeric($special_price) || $special_price < 0)) { 
                $this->_fault('price_validation');
            } 
            if(isset($special_from_date) && !empty($special_from_date) && !$this->validateDate($special_from_date)) { 
                $this->_fault('date_not_valid');
            }  
            if(isset($special_to_date) && !empty($special_to_date) && !$this->validateDate($special_to_date)) { 
                $this->_fault('date_not_valid');
            }  


            Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);
            try{
                    $product->load($product->getId());
                    $product->setData('price',$price);
                    if(isset($special_price) && !empty($special_price)){
                        $product->setData('special_price',$special_price);
                        $product->setData('special_from_date',$special_from_date);
                        $product->setData('special_to_date',$special_to_date);
                    }
                	$product->save();
                    return true;
                
            }catch(Exception $ex){
                $this->_fault('exception_error',$ex->getMessage());
            }
		}else{
			$this->_fault('product_not_found');
		}			
    	
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date; //die;
    }


    public function updateStock($sku,$qty,$reason)
    {
        //print_r($reason);die;
        
        if(!isset($sku) || empty($sku)){
            $this->_fault('sku_not_found');
        }

        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
        if($product){
            if(!isset($qty) || empty($qty)){
                $this->_fault('qty_not_defined');
            }
            if(isset($qty)  && (!is_numeric($qty))){
                $this->_fault('qty_validation');
            }
            if(!isset($reason) || empty($reason)){
                $this->_fault('reason_not_found');
            }
            
           $product->cleanModelCache();
            
            Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);
            try{
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                $changedQty = $stockItem->getQty() + (int)$qty; 
                $stockItem->setQty($changedQty);
                $stockItem->setIsInStock((int)($changedQty > 0));
                $product->setQtyUpdateReason($reason);
                $product->setQtyUpdateFlag(1);

                //Save Data
                $product->save();
                if($stockItem->save()){
                    $updates = array('product_id'=>$product->getId(),
                                    'stock_item_id'=>$stockItem->getItemId(),
                                    'qty_from_navision'=>(int)$qty,
                                    'reason' => $reason,
                                    'updated_at'=> Mage::getModel('core/date')->date('Y-m-d H:i:s'));
                    
                    $inventoryUpdates = Mage::getModel('soapapi/inventoryupdates')->setData($updates)->save();
                    return true;
                }else{
                    $this->_fault('stock_not_updated');
                }
            }catch(Exception $ex){
                $this->_fault('exception_error',$ex->getMessage());
            }
        }else{
            $this->_fault('product_not_found');
        }           
        
    }

}