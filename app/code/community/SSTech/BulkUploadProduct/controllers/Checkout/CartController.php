<?php
require_once 'Mage/Checkout/controllers/CartController.php';

class SSTech_BulkUploadProduct_Checkout_CartController extends Mage_Checkout_CartController
{
	/**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */

	protected function _initProduct($sku)
	{
		$productId = Mage::getModel('catalog/product')->getIdBySku($sku);	
		if ($productId) {
			$product = Mage::getModel('catalog/product')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($productId);

			if ($product->getId() 
				&& $product->isSaleable() 
				&& $this->isEnabled($product->getStatus()) 
				//&& $product->isVisibleInSiteVisibility()
				) {
				return $product;
			}
		}
		return false;
	}

	public function isEnabled($status)
	{
		return $status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
	}


	/**
     * Add product to shopping cart action
     *
     * @return Mage_Core_Controller_Varien_Action
     * @throws Exception
     */

    public function addAction()
    {    
        $added = array();
        $notAdded = array();
        $cart   = $this->_getCart();
        $products = $this->getRequest()->getPost();
        //print_r($products);
        //exit;



		//if ($val[0] == '') { continue; }
        $params['sku'] = $products['item'];
        $params['qty'] = $products['qty'];
        $product = $this->_initProduct($params['sku']);

        /**
         * Check product availability
         */
        if (!$product) {
                if (array_key_exists($params['sku'], $notAdded)) {
                    $notAdded[$params['sku']] = $notAdded[$params['sku']] + $params['qty'];
                }else{
                    $notAdded[$params['sku']] = $params['qty'];
                }
        }else{
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE){
                
                try{
                    $cart->addProduct($product, $params);
                }catch(Exception $e){
                    if (array_key_exists($params['sku'], $notAdded)) {
                    $notAdded[$params['sku']] = $notAdded[$params['sku']] + $params['qty'];
                    }else{
                        $notAdded[$params['sku']] = $params['qty'];
                    }

                    $html = $this->getLayout()->createBlock('bulkuploadproduct/index')->setTemplate('bulkuploadproduct/bulkuploadproductpopup.phtml')->toHtml();
                    $this->getResponse()->setBody($html);
                    return;

                } 

                if (array_key_exists($params['sku'], $added)) {
                    $added[$params['sku']] = $added[$params['sku']] + $params['qty'];
                }else{
                    $added[$params['sku']] = $params['qty'];
                }
            }else{
                if (array_key_exists($params['sku'], $notAdded)) {
                    $notAdded[$params['sku']] = $notAdded[$params['sku']] + $params['qty'];
                }else{
                    $notAdded[$params['sku']] = $params['qty'];
                }
            }
        }
        
        Mage::register('product-added-to-bulk-order', $added);
        Mage::register('product-not-added-to-bulk-order', $notAdded);
        $cart->save();
        $html = $this->getLayout()->createBlock('bulkuploadproduct/index')->setTemplate('bulkuploadproduct/bulkuploadproductpopup.phtml')->toHtml();
		$this->getResponse()->setBody($html);
        return;
    }
   

    /**
     * Add product to shopping cart action
     *
     * @return Mage_Core_Controller_Varien_Action
     * @throws Exception
     */

    public function addBoxAction()
    {    

        $added = array();
        $notAdded = array();
        $cart   = $this->_getCart();
        $post = $this->getRequest()->getPost('bulk-order-sku');
        $product = explode(';' , $post);        
        foreach ($product as $key => $value) {
            $val = explode(',' , $value);
			if ($val[0] == '') { continue; }
            $params['sku'] = filter_var($val[0]);
            $params['qty'] = filter_var($val[1]);    
            $product = $this->_initProduct($params['sku']);
            /**
             * Check product availability
             */
            if (!$product) {
                if (array_key_exists($params['sku'], $notAdded)) {
                        $notAdded[$params['sku']] = $notAdded[$params['sku']] + $params['qty'];
                    }else{
                        $notAdded[$params['sku']] = $params['qty'];
                    }
            }else{
                if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE){
                    $cart->addProduct($product, $params);  
                    if (array_key_exists($params['sku'], $added)) {
                        $added[$params['sku']] = $added[$params['sku']] + $params['qty'];
                    }else{
                        $added[$params['sku']] = $params['qty'];
                    }
                }else{
                    if (array_key_exists($params['sku'], $notAdded)) {
                        $notAdded[$params['sku']] = $notAdded[$params['sku']] + $params['qty'];
                    }else{
                        $notAdded[$params['sku']] = $params['qty'];
                    }
                }
            }
        }
        Mage::register('product-added-to-bulk-order', $added);
        Mage::register('product-not-added-to-bulk-order', $notAdded);
        $cart->save();
        $html = $this->getLayout()->createBlock('bulkuploadproduct/index')->setTemplate('bulkuploadproduct/bulkuploadproductpopup.phtml')->toHtml();
		$this->getResponse()->setBody($html);
        return;
    }
}

