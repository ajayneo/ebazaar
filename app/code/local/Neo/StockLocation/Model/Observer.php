<?php
    class Neo_StockLocation_Model_Observer
    {
        public function sales_order_invoice_save_after(Varien_Event_Observer $observer)
        {
            $invoice = $observer->getEvent()->getInvoice();
            
           // echo $invoice->getId();die;
            $postData = Mage::app()->getRequest()->getPost();
            if (!empty($postData)) 
            {
                $stockLocationModelRaw = Mage::getModel('stocklocation/location');  
                //print_r($stockLocationModelRaw);               
                $orderId = (Mage::app()->getRequest()->getParam('order_id', '') ? Mage::app()->getRequest()->getParam('order_id', '') : $_REQUEST['order_id']);            
                //code added by JP for issue found while creating invoice & shipment for vow delight module.
                if($orderId == '')
                {
                   $order   = $invoice->getOrder();
                   $orderId = $order->getId();                                       
                }                     
                //$stockLocationModel = $stockLocationModelRaw->getCollection()->addFieldToSelect(array('stock_location','id'))->addFieldToFilter('order_id',$orderId)->getFirstItem()->getData();

                /*if(isset($postData['stock_location']) && strlen($stockLocationModel['stock_location']) > 0 && $stockLocationModel['stock_location'] == $postData['stock_location'])
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
                {*/
                    $data_save = array();
                    if(isset($postData['stock_location']) && strlen($postData['stock_location']) > 0)
                    { 
                        $data = array('order_id'=>$orderId,'invoice_id'=>$invoice->getId(),'stock_location'=> $postData['stock_location']);
                        $data_save['order_id'] = $orderId;
                        $data_save['invoice_id'] = $invoice->getId();
                        $data_save['stock_location'] = $postData['stock_location'];
                        
                       
                        $model = Mage::getModel('stocklocation/location')->load();
                        $model->addData($data_save);
                        $model->save();
                        $productData = array();
                        foreach($invoice->getAllItems() as $item) {
                            $productData[$item->getOrderItemId()] = $item->getQty();
                        }
                        if($postData['stock_location'] == 'Tamilnadu Warehouse'){
                            

                            foreach ($productData as $key => $value) {
                                $orderItem = Mage::getModel('sales/order_item')->load($key);
                                $orderSku = $orderItem->getSku();
                                $stockchennai = Mage::getModel('ebautomation/stockchennai')->load($orderSku,'sku');
                                $newQty = $stockchennai->getQty() - $value;
                                $stockchennai->setQty($newQty);
                                $stockchennai->save();
                            }
                        }

                        return;
                    }
                    else 
                    {
                        Mage::throwException(Mage::helper('stocklocation')->__('For creating invoice pleas sellect stock location from the drop down below.')); 
                        return;
                    }
                return $this;
                //}
            }
        }
    }
?>