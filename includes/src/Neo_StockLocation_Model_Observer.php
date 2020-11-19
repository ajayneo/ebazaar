<?php
    class Neo_StockLocation_Model_Observer
    {
        public function sales_order_invoice_save_after(Varien_Event_Observer $observer)
        {
            $postData = Mage::app()->getRequest()->getPost();
            if (!empty($postData)) 
            {
                $stockLocationModelRaw = Mage::getModel('stocklocation/location');                
                $orderId = (Mage::app()->getRequest()->getParam('order_id', '') ? Mage::app()->getRequest()->getParam('order_id', '') : $_REQUEST['order_id']);            
                $stockLocationModel = $stockLocationModelRaw->getCollection()->addFieldToSelect(array('stock_location','id'))->addFieldToFilter('order_id',$orderId)->getFirstItem()->getData();

                if(isset($postData['stock_location']) && strlen($stockLocationModel['stock_location']) > 0 && $stockLocationModel['stock_location'] == $postData['stock_location'])
                {
                    return;
                }
                elseif(isset($postData['stock_location']) && strlen($postData['stock_location']) > 0 && strlen($stockLocationModel['stock_location']) > 0 && $stockLocationModel['stock_location'] != $postData['stock_location'])
                {
                    $stockLocationModelRaw->setStockLocation($postData['stock_location']);
                    $stockLocationModelRaw->setId($stockLocationModel['id'])->save();
                    return;
                }
                else 
                {
                    if(isset($postData['stock_location']) && strlen($postData['stock_location']) > 0)
                    {
                        $data = array('order_id'=>$orderId,'stock_location'=> $postData['stock_location']);
                        $stockLocationModelRaw->setData($data);
                        $stockLocationModelRaw->save();
                        return;
                    }
                    else 
                    {
                        Mage::throwException(Mage::helper('stocklocation')->__('For creating invoice pleas sellect stock location from the drop down below.')); 
                        return;
                    }

                }
            }
        }
    }
?>