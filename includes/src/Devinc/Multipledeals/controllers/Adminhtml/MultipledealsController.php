<?php

class Devinc_Multipledeals_Adminhtml_MultipledealsController extends Mage_Adminhtml_Controller_Action
{
	const STATUS_RUNNING = Devinc_Multipledeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Multipledeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Multipledeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Multipledeals_Model_Source_Status::STATUS_QUEUED;

	/**
	 * Added this function by pradeep sanku on 14th August since patch SUPEE 6285 was having access to some 3rd party extension
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('multipledeals/items');
	}
	
	/**
     * Initialize deals grid
     */
	public function indexAction() {
		$this->loadLayout()
			->_setActiveMenu('multipledeals/items')
        	->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Deals'), Mage::helper('adminhtml')->__('Manage Deals'));
       	if (Mage::helper('multipledeals')->getMagentoVersion()>1330) $this->_title($this->__('Manage Deals'));
		$this->renderLayout();
	}

    /**
     * deals grid for ajax reload
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
 
	public function newAction() {
		$this->_forward('edit');
	}
	
    /**
     * edit deal
     */
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('multipledeals/multipledeals')->load($id);

		if ($model->getId() || $id == 0) {
			$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('multipledeals_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('multipledeals/items');
			if (Mage::helper('multipledeals')->getMagentoVersion()>1330) {
				if ($model->getId()) {
					$this->_title('Edit Deal');
				} else {
					$this->_title('New Deal');			
				}
			}

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit'))
				->_addLeft($this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_tabs'));

			$this->renderLayout();
		} else {
			$this->_getSession()->addError(Mage::helper('multipledeals')->__('Deal does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
    /**
     * products grid for ajax reload on deal edit page
     */
    public function productsAction()
    {
		$this->getResponse()->setBody(
            $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_tab_products')->toHtml()
        );		
    }
	
    /**
     * save deal
     */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			//format dates
			$data['datetime_from'] = Mage::getModel('multipledeals/entity_attribute_backend_datetime')->formatDate($data['datetime_from']); 
			$data['datetime_to'] = Mage::getModel('multipledeals/entity_attribute_backend_datetime')->formatDate($data['datetime_to']); 				
	  		
			if (!isset($data['deal_qty'])) $data['deal_qty'] = 0;			
			if (!isset($data['deal_price'])) $data['deal_price'] = 0;			
			if (!isset($data['position']) || $data['position']=='') $data['position'] = 0;	
			if (isset($data['stores'])) $data['stores'] = implode(',',$data['stores']);
								
			$model = Mage::getModel('multipledeals/multipledeals');				
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {		
				$model->save();		
				$productId = $model->getProductId();	
				
				//verify if product is enabled for at least one website
				$productStatus = 2;
				$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
			    $statusId = $eavAttribute->getIdByCode('catalog_product', 'status');

			    $resource = Mage::getSingleton('core/resource');
			    $connection = $resource->getConnection('core_read');

			    $select = $connection->select()
			    ->from($resource->getTableName('catalog_product_entity_int'))
			    ->where('store_id IN (?)', '0,'.$model->getStores())
			    ->where('attribute_id = ?', $statusId)
			    ->where('value = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			    ->where('entity_id = ?', $model->getProductId());

				$rows = $connection->fetchAll($select);

				if (count($rows)>0) {
					$productStatus = 1;
				}	
				
				//verify if product is in stock and if deal qty is higher than 0
				$product = Mage::getModel('catalog/product')->load($productId);
				$dealQtyValidationTypes = array('simple', 'virtual', 'downloadable');		
				$stockItem = $product->getStockItem();		    		
				
				if ($stockItem->getIsInStock()) {
				    if (in_array($product->getTypeId(), $dealQtyValidationTypes)) {
				    	$inStock = ($model->getDealQty()>0) ? true : false;
				    } else {
				    	$inStock = true;						
				    }
				} else {
				    $inStock = false;
				}
			
				//disable deal if product is disabled or not in stock
				if ($inStock && $productStatus==1) {
					$this->_getSession()->addSuccess(Mage::helper('multipledeals')->__('Deal was successfully saved'));
				} elseif ($productStatus!=1) {
					$this->_getSession()->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because the product is disabled.'));		
					$model->setStatus(self::STATUS_DISABLED)->save();			
				} elseif (!$inStock) {
					if ($stockItem->getIsInStock()) {
						$this->_getSession()->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because the deal\'s qty is 0.'));		
					} else {
						$this->_getSession()->addError(Mage::helper('multipledeals')->__('Deal was saved & disabled because the product is out of stock.'));		
					}
					$model->setStatus(self::STATUS_DISABLED)->save();		
				} 
				
				$this->_getSession()->setFormData(false);
					
				//update deal status
				$model->setDealJustSaved(true);
				Mage::getModel('multipledeals/multipledeals')->refreshDeal($model);

				if ($this->getRequest()->getParam('back')) {					
					//get the page number for the "Select a Product" tab on the deal edit page
					$pageNr = Mage::helper('multipledeals')->getProductPage($productId);
					
					$this->_redirect('*/*/edit', array('id' => $model->getId(), 'page' => $pageNr));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
				
				//get the page number for the "Select a Product" tab on the deal edit page
                if (($productId = $model->getProductId())>0) {
					$pageNr = Mage::helper('multipledeals')->getProductPage($productId);
                } else {
                	$pageNr = 1;
                }
                
                $this->_redirect('*/*/edit', array('id' => $model->getId(), 'page' => $pageNr));
                return;
            }
        }
        $this->_getSession()->addError(Mage::helper('multipledeals')->__('Unable to find deal to save.'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if ($dealId = $this->getRequest()->getParam('id', false)) {
			$deal = Mage::getModel('multipledeals/multipledeals')->load($dealId);
			try {				 
				$deal->delete();					 
				$this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted.'));
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('_current' => true));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $dealIds = $this->getRequest()->getParam('multipledeals');
        if(!is_array($dealIds)) {
			$this->_getSession()->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($dealIds as $dealId) {
                    $deal = Mage::getModel('multipledeals/multipledeals')->load($dealId);
                    $deal->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($dealIds)
                    )
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }	
    
    public function massStatusAction()
    {
        $dealIds = $this->getRequest()->getParam('multipledeals');
        if(!is_array($dealIds)) {
            $this->_getSession()->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($dealIds as $dealId) {
                    $deal = Mage::getSingleton('multipledeals/multipledeals')
                        ->load($dealId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($dealIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
		Mage::getModel('multipledeals/multipledeals')->refreshDeals();
		
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'Deals.csv';
        $content    = $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, strip_tags($content));
    }

    public function exportXmlAction()
    {
        $fileName   = 'Deals.xml';
        $content    = $this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }	

}