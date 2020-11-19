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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout observer model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Multipledeals_Model_Observer
{		
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    protected $_checkedQuoteItems = array();
    
	//delete deal if product is deleted from catalog
	public function deleteDeal($observer)
    {
		$productId = $observer->getEvent()->getProduct()->getId();		
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id', $productId);		
		
		if (count($dealCollection)>0) {
			foreach ($dealCollection as $deal) {
				$deal->delete();
			}
		}
	}
	
	//refresh deals and redirect the homepage to the deal page
	public function refreshDealsHomepageRedirect($observer)
    {		
		$helper = Mage::helper('multipledeals');
    
		//refresh deals
		if ($helper->isEnabled() && Mage::getStoreConfig('multipledeals/configuration/refresh_rate')!=0 && $this->_requiresRefresh()) {
			Mage::getModel('multipledeals/multipledeals')->refreshDeals(); 
		}
    
		//redirect to main deal if option enabled and if on homepage
		$url = Mage::helper('core/url')->getCurrentUrl();
		$baseUrl = Mage::getBaseUrl();
		$baseUrlNoIndex = str_replace('index.php/', '', Mage::getBaseUrl());
		$path = str_replace($baseUrlNoIndex, '', str_replace($baseUrl, '', $url));	
		if ($path == '' || $path == '/' || $path == '/index.php' || $path == '/index.php/' || $path == '/home' || $path == '/home/') {	
	        $storeId = Mage::app()->getStore()->getId();
			$redirectEnabled = Mage::getStoreConfig('multipledeals/configuration/redirect', $storeId);
			
			if ($helper->isEnabled() && $redirectEnabled) {   
				$mainDeal = Mage::helper('multipledeals')->getDeal();				
				if ($mainDeal) {
					$response = $observer->getEvent()->getResponse();
					$product = Mage::getModel('catalog/product')->load($mainDeal->getProductId());				
					$product->setDoNotUseCategoryId(true);
					$response->setRedirect($product->getProductUrl());
				}
			}
		}
	}
	
	protected function _requiresRefresh() {
		$resource = Mage::getSingleton('core/resource');
	    $connection = $resource->getConnection('core_read');

	    $select = $connection->select()
	    ->from($resource->getTableName('core_config_data'))
	    ->where('scope = ?', 'default')
	    ->where('scope_id = ?', 0)
	    ->where('path = ?', 'multipledeals/refresh');

		$rows = $connection->fetchAll($select); 
		
		if (count($rows)>0) {
			$refreshDateTime = trim($rows[0]['value']);
			if ($this->_getCurrentDateTime()>$refreshDateTime) {
			    $this->_saveRefresh();
			    return true;
			} else {
			    return false;
			}
		} else {
			$this->_saveRefresh();
			return true;
		}
	}
	
	protected function _getCurrentDateTime($_storeId = null, $_format = 'Y-m-d H:i:s') {
		if (is_null($_storeId)) {
			$_storeId = Mage::app()->getStore()->getId();
		}
		$storeDatetime = new DateTime();
		$storeDatetime->setTimezone(new DateTimeZone(Mage::getStoreConfig('general/locale/timezone', $_storeId)));	
		
		return $storeDatetime->format($_format);
	}
	
	protected function _saveRefresh() {
		$nextVerificationDate = date('Y-m-d H:i:s', strtotime($this->_getCurrentDateTime().'+'.Mage::getStoreConfig('multipledeals/configuration/refresh_rate').' minutes'));
		Mage::getModel('core/config')->saveConfig('multipledeals/refresh', $nextVerificationDate, 'default', 0);	
	}
    
	//runs at checkout pages. checks to see if the deals have reached their maximum qty
	public function reviewCartItem($observer) {	
		if (Mage::helper('multipledeals')->isEnabled()) {	
			$item = $observer->getEvent()->getItem();	
			$quote = $item->getQuote();
			$product = Mage::getModel('catalog/product')->load($item->getProductId());	
			$helper = Mage::helper('multipledeals');
			$deal = $helper->getDealByProduct($product);
			
			if ($deal) {	
				$checkQtyForType = array('simple', 'virtual', 'downloadable');	
				$totalQty = $this->_getQuoteItemQtyForCheck($product->getId(), $item->getId(), $item->getQty());     
			    $maxQty = $deal->getDealQty();
			    
			    if (in_array($product->getTypeId(), $checkQtyForType) && $maxQty<$totalQty) {
			    	$message = $helper->__('The maximum order qty available for the "%s" DEAL is %s.', $product->getName(), $maxQty);
                	$item->setHasError(true);
                	$item->setMessage($message);
			    	$quote->setHasError(true);
			    	$quote->addMessage($message);
			    }     			   						
			} 	
		}
	}
	
	//retrieves the total product qty from the cart; also taking into account the qty of associated products or custom options from the cart
    protected function _getQuoteItemQtyForCheck($productId, $quoteItemId, $itemQty)
    {
        $qty = $itemQty;
        if (isset($this->_checkedQuoteItems[$productId]['qty']) &&
            !in_array($quoteItemId, $this->_checkedQuoteItems[$productId]['items'])) {
                $qty += $this->_checkedQuoteItems[$productId]['qty'];
        }
		
        $this->_checkedQuoteItems[$productId]['qty'] = $qty;
        $this->_checkedQuoteItems[$productId]['items'][] = $quoteItemId;

        return $qty;
    }
	
	//updates the deals available qty and it's sold qty
	public function updateDealQty($observer)
    {
		$items = $observer->getEvent()->getOrder()->getItemsCollection();		
		foreach ($items as $item) {			
			$helper = Mage::helper('multipledeals');	
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			$deal = $helper->getDealByProduct($product);
			$checkQtyForType = array('simple', 'virtual', 'downloadable', 'configurable');	
			
			if ($helper->isEnabled() && $deal && in_array($product->getTypeId(), $checkQtyForType)) {	
				$newQty = $deal->getDealQty()-$item->getQtyOrdered();
				$newSoldQty = $deal->getQtySold()+$item->getQtyOrdered();				
				
				$deal->setDealQty($newQty)->setQtySold($newSoldQty)->save();	
				Mage::getModel('multipledeals/multipledeals')->refreshDeal($deal);
			}
		}
	}
	
	//sets the deal price to the product
	public function getFinalPrice($observer)
    {
    	$product = $observer->getEvent()->getProduct();
    	$qty = $observer->getEvent()->getQty();
		$helper = Mage::helper('multipledeals');
		$deal = $helper->getDealByProduct($product);
		$currentDateTime = Mage::helper('multipledeals')->getCurrentDateTime(0);
		
		if ($helper->isEnabled() && $deal) {	
			$setPriceForType = array('simple', 'virtual', 'downloadable', 'configurable');	
			if ($currentDateTime>=$deal->getDatetimeFrom() && $currentDateTime<=$deal->getDatetimeTo()) {
				if (in_array($product->getTypeId(), $setPriceForType)) {
					$price = $this->_applyTierPrice($product, $qty, $deal->getDealPrice());
					$product->setFinalPrice($price);	
				} 
			} else {
				Mage::getModel('multipledeals/multipledeals')->refreshDeal($deal);				
			}
		}
	}
	
	public function setCollectionFinalPrice($observer)
    {
    	$products = $observer->getEvent()->getCollection();
		$currentDateTime = Mage::helper('multipledeals')->getCurrentDateTime(0);
		
    	foreach ($products as $product) {
			$helper = Mage::helper('multipledeals');
			$deal = $helper->getDealByProduct($product);
			
			if ($helper->isEnabled() && $deal) {	
				$setPriceForType = array('simple', 'virtual', 'downloadable', 'configurable');	
				if ($currentDateTime>=$deal->getDatetimeFrom() && $currentDateTime<=$deal->getDatetimeTo()) {
					if (in_array($product->getTypeId(), $setPriceForType)) {
						$price = $this->_applyTierPrice($product, null, $deal->getDealPrice());
						$product->setFinalPrice($price);	
					}
				} else {
					Mage::getModel('multipledeals/multipledeals')->refreshDeal($deal);				
				}
			}
		}
	}
	
	protected function _applyTierPrice($product, $qty, $finalPrice)
    {
        if (is_null($qty)) {
            return $finalPrice;
        }

        $tierPrice  = $product->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
            $finalPrice = min($finalPrice, $tierPrice);
        }
        return $finalPrice;
    }
    
}
