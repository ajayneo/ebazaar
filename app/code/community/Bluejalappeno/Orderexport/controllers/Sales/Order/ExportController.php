<?php
/**
 * Magento Bluejalappeno Order Export Module
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
 * @category   Bluejalappeno
 * @package    Bluejalappeno_OrderExport
 * @copyright  Copyright (c) 2010 Wimbolt Ltd (http://www.bluejalappeno.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Genevieve Eddison, Jonathan Feist, Farai Kanyepi <sales@bluejalappeno.com>
 * */
class Bluejalappeno_Orderexport_Sales_Order_ExportController extends Mage_Adminhtml_Controller_Action
{

	/**
	* Added this function by pradeep sanku on 12th August since patch SUPEE 6285 was having access to some 3rd party extension
	*/
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('system/config/order_export');
	}
	
    /**
     * Exports orders defined by id in post param "order_ids" to csv and offers file directly for download
     * when finished.
     */
    public function csvexportAction()
    {
    	$orders = $this->getRequest()->getPost('order_ids', array());

		switch(Mage::getStoreConfig('order_export/export_orders/output_type')){
			case 'Standard':
				$file = Mage::getModel('bluejalappeno_orderexport/export_csv')->exportOrders($orders);
				$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
				break;
			case 'Sage':
				$file = Mage::getModel('bluejalappeno_orderexport/export_sage')->exportOrders($orders);
				$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
				break;
			case 'Highrise':
				$failedList = '';
    			$successCount = 0;
    			$failCount = 0;
            	try {
                	$results = Mage::getModel('bluejalappeno_orderexport/export_highrise')->exportOrders($orders);
	            	foreach ($results as $orderid => $status) {
	    				if ($status > 0 ) $successCount++;
	    				else {
	    					$failedList.= $orderid .' ';
	    					$failCount++;
	    				}
	    			}
            	}
            	catch (Exception $e) {
            		Mage::log($e->getMessage());
            		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sales')->__($e->getMessage()));
            	}

	        	if ($failCount > 0) {
	        		$failedString = $successCount .' order(s) have been imported. The following orders failed to import: ' .$failedList;
	        		$this->_getSession()->addError($this->__( $failedString));

	        	}
	        	else {
	            	$this->_getSession()->addSuccess($this->__('%s order(s) have been imported', $successCount));
	        	}
	        	$this->_redirect('*/sales_order');
				//$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
				break;
		}
    }
    
     public function csvexports_backupAction() {
        $ordersIds = $this->getRequest()->getPost('order_ids', array());
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
        $orders = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToSelect('*');
        
        $affiliate_model = Mage::getModel('neoaffiliate/affiliate')->getCollection();

        $allOrderData = array();
        foreach ($orders as $order) {
            if (in_array($order['entity_id'], $ordersIds))
            {
                $billingAddress = $order->getBillingAddress()->getData(); 
                
                if ($order['increment_id']) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);
                    //echo "<pre>";print_r($order->getData());exit;
                    
                    $items = $order->getAllVisibleItems();
                    $affiliate_model_data = $affiliate_model->addFieldToSelect('iscstused')->addFieldToFilter('order_id',$order['entity_id'])->getFirstItem()->getData();

                    if (count($items) == 1) {
                        foreach ($items as $i) {
                            if ($order['created_at']) {
                                $allOrderData[$order['entity_id']]['created_at'] = $order['created_at'];
                            } else {
                                $allOrderData[$order['entity_id']]['created_at'] = '';
                            }
                            if ($order['created_at']) {
                                $allOrderData[$order['entity_id']]['orderId'] = $order['increment_id'];
                            } else {
                                $allOrderData[$order['entity_id']]['orderId'] = '';
                            }
                            if ($order['subtotal']) {
                                $allOrderData[$order['entity_id']]['subtotal'] = $order['grand_total'];
                            } else {
                                $allOrderData[$order['entity_id']]['subtotal'] = '';
                            }
                            if ($order['customer_firstname']) {
                                $allOrderData[$order['entity_id']]['customer_firstname'] = $order['customer_firstname'].' '.$order['customer_lastname'];
                            } else {
                                $allOrderData[$order['entity_id']]['customer_firstname'] = '';
                            }
                            if ($billingAddress['postcode']) {
                                $allOrderData[$order['entity_id']]['address'] = $billingAddress['street'].' '.$billingAddress['city'].' '.$billingAddress['postcode'].' '.$billingAddress['region'];
                            } else {
                                $allOrderData[$order['entity_id']]['address'] = '';
                            }
                            if ($billingAddress['city']) {
                                $allOrderData[$order['entity_id']]['subtotal2'] = $billingAddress['city'];
                            } else {
                                $allOrderData[$order['entity_id']]['subtotal2'] = '';
                            }
                            if ($order['status']) {
                                $allOrderData[$order['entity_id']]['subtotal1'] = $order['status'];
                            } else {
                                $allOrderData[$order['entity_id']]['subtotal1'] = '';
                            }
                            
                            $allOrderData[$order['entity_id']]['name'] = $i->getName();
                            $allOrderData[$order['entity_id']]['qty'] = $i->getQtyOrdered();
                            $allOrderData[$order['entity_id']]['price'] = $i->getRowTotal() - $i->getDiscountAmount() + $i->getTaxAmount();  

                            if (!empty($affiliate_model_data['iscstused'])) {
                                $allOrderData[$order['entity_id']]['cst'] = $affiliate_model_data['iscstused'];
                            } else {
                                $allOrderData[$order['entity_id']]['cst'] = 'No';
                            } 
                        }
                    } else {
                        foreach ($items as $i) {
                            //echo "==<pre>";print_r($i->getData());exit;
                            $entity_key = $order['entity_id'] . '_' . $i->getSku();
                            if ($order['created_at']) {
                            $allOrderData[$entity_key]['created_at'] = $order['created_at'];
                            } else {
                                $allOrderData[$entity_key]['created_at'] = '';
                            }
                            if ($order['increment_id']) {
                                $allOrderData[$entity_key]['orderId'] = $order['increment_id'];
                            } else {
                                $allOrderData[$entity_key]['orderId'] = '';
                            }
                            if ($order['subtotal']) {
                                $allOrderData[$entity_key]['subtotal'] = $order['grand_total'];
                            } else {
                                $allOrderData[$entity_key]['subtotal'] = '';
                            }
                            if ($order['customer_firstname']) {
                                $allOrderData[$entity_key]['customer_firstname'] = $order['customer_firstname'].' '.$order['customer_lastname'];
                            } else {
                                $allOrderData[$entity_key]['customer_firstname'] = '';
                            }
                            if ($billingAddress['postcode']) {
                                $allOrderData[$entity_key]['address'] = $billingAddress['street'].' '.$billingAddress['city'].' '.$billingAddress['postcode'].' '.$billingAddress['region'];
                            } else {
                                $allOrderData[$entity_key]['address'] = ''; 
                            }
                            if ($billingAddress['city']) {
                                $allOrderData[$entity_key]['subtotal2'] = $billingAddress['city'];
                            } else {
                                $allOrderData[$entity_key]['subtotal2'] = ''; 
                            }
                            if ($order['status']) {
                                $allOrderData[$entity_key]['subtotal1'] = $order['status'];
                            } else {
                                $allOrderData[$entity_key]['subtotal1'] = ''; 
                            }
                            
                            $allOrderData[$entity_key]['name'] = $i->getName();
                            $allOrderData[$entity_key]['qty'] = $i->getQtyOrdered();
                            $allOrderData[$entity_key]['price'] = $i->getRowTotal() - $i->getDiscountAmount() + $i->getTaxAmount();  

                            if (!empty($affiliate_model_data['iscstused'])) {
                                $allOrderData[$entity_key]['cst'] = $affiliate_model_data['iscstused'];
                            } else {
                                $allOrderData[$entity_key]['cst'] = 'No';  
                            }

                        }
                    } 
                }
            }
        }

        $csvHead = array('Date','Order Number','Total Price','Name','Address','City','Status','Product', 'Qty', 'Row Total', 'IS CST Used');
        $this->convert_to_csv($csvHead, $allOrderData, 'CUSTOM_ORDER_EXPORT_DATA.csv', ',');
    }
    
    
    protected function convert_to_csv($csvHead, $input_array, $output_file_name, $delimiter) {
            $f = fopen('php://memory', 'w');
            fputcsv($f, $csvHead, $delimiter);
            foreach ($input_array as $line) {
                fputcsv($f, $line, $delimiter);
            }
            fseek($f, 0);
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
            fpassthru($f);
        }

        /**
     * Exports orders defined by id in post param "order_ids" to csv and offers file directly for download
     * when finished.
     */
    public function csvexportcformAction()
    {
        $order = $this->getRequest()->getPost('order_ids', array());

        $collection = Mage::getModel('neoaffiliate/affiliate')->getCollection()->addFieldToSelect('order_id')->addFieldToFilter('affiliate_id',$order)->getData();

        foreach ($collection as $key => $value){
            $singleArray[$key] = $value['order_id'];
        }

        $orders = array_unique($singleArray);

        switch(Mage::getStoreConfig('order_export/export_orders/output_type')){
            case 'Standard':
                $file = Mage::getModel('bluejalappeno_orderexport/export_csvaff')->exportOrders($orders);
                $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
            case 'Sage':
                $file = Mage::getModel('bluejalappeno_orderexport/export_sage')->exportOrders($orders);
                $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
            case 'Highrise':
                $failedList = '';
                $successCount = 0;
                $failCount = 0;
                try {
                    $results = Mage::getModel('bluejalappeno_orderexport/export_highrise')->exportOrders($orders);
                    foreach ($results as $orderid => $status) {
                        if ($status > 0 ) $successCount++;
                        else {
                            $failedList.= $orderid .' ';
                            $failCount++;
                        }
                    }
                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sales')->__($e->getMessage()));
                }

                if ($failCount > 0) {
                    $failedString = $successCount .' order(s) have been imported. The following orders failed to import: ' .$failedList;
                    $this->_getSession()->addError($this->__( $failedString));

                }
                else {
                    $this->_getSession()->addSuccess($this->__('%s order(s) have been imported', $successCount));
                }
                $this->_redirect('*/sales_order');
                //$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
        }
    }

    public function csvexportsAction()
    {
        $orders = $this->getRequest()->getPost('order_ids', array());

        switch(Mage::getStoreConfig('order_export/export_orders/output_type')){
            case 'Standard':
                $file = Mage::getModel('bluejalappeno_orderexport/export_csvcustom')->exportOrders($orders);
                $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
            case 'Sage':
                $file = Mage::getModel('bluejalappeno_orderexport/export_sage')->exportOrders($orders);
                $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
            case 'Highrise':
                $failedList = '';
                $successCount = 0;
                $failCount = 0;
                try {
                    $results = Mage::getModel('bluejalappeno_orderexport/export_highrise')->exportOrders($orders);
                    foreach ($results as $orderid => $status) {
                        if ($status > 0 ) $successCount++;
                        else {
                            $failedList.= $orderid .' ';
                            $failCount++;
                        }
                    }
                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sales')->__($e->getMessage()));
                }

                if ($failCount > 0) {
                    $failedString = $successCount .' order(s) have been imported. The following orders failed to import: ' .$failedList;
                    $this->_getSession()->addError($this->__( $failedString));

                }
                else {
                    $this->_getSession()->addSuccess($this->__('%s order(s) have been imported', $successCount));
                }
                $this->_redirect('*/sales_order');
                //$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
        }
    }

    public function csvexportssAction()
    {
        $orders = $this->getRequest()->getPost('order_ids', array());

        switch(Mage::getStoreConfig('order_export/export_orders/output_type')){
            case 'Standard':
                $file = Mage::getModel('bluejalappeno_orderexport/export_csvnav')->exportOrders($orders);
                $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
            case 'Sage':
                $file = Mage::getModel('bluejalappeno_orderexport/export_sage')->exportOrders($orders);
                $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
            case 'Highrise':
                $failedList = '';
                $successCount = 0;
                $failCount = 0;
                try {
                    $results = Mage::getModel('bluejalappeno_orderexport/export_highrise')->exportOrders($orders);
                    foreach ($results as $orderid => $status) {
                        if ($status > 0 ) $successCount++;
                        else {
                            $failedList.= $orderid .' ';
                            $failCount++;
                        }
                    }
                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sales')->__($e->getMessage()));
                }

                if ($failCount > 0) {
                    $failedString = $successCount .' order(s) have been imported. The following orders failed to import: ' .$failedList;
                    $this->_getSession()->addError($this->__( $failedString));

                }
                else {
                    $this->_getSession()->addSuccess($this->__('%s order(s) have been imported', $successCount));
                }
                $this->_redirect('*/sales_order');
                //$this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
                break;
        }
    }

    public function csvexportnewAction()
    {
        $orders = $this->getRequest()->getPost('order_ids', array());
        $file = Mage::getModel('bluejalappeno_orderexport/export_csvnew')->exportOrders($orders);
        $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
    }

}
?>