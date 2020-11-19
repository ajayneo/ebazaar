<?php
/**
* Placeholder container for homepage special products block
*
* @category    Mymodule
* @package     Oggetto_Mymodule
*/
class Devinc_Multipledeals_Model_Container_Multipledeals extends Enterprise_PageCache_Model_Container_Abstract
{
	/**
	* Get customer identifier from cookies
	*
	* @return string
	*/
	protected function _getIdentifier()
	{
		return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
	}
	 
	/**
	* Get cache identifier
	*
	* @return string
	*/
	protected function _getCacheId()
	{
		return 'MULTIPLEDEALS_MULTIPLEDEALS' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
	}
	 
	/**
	* Render block content
	*
	* @return string
	*/
	protected function _renderBlock()
	{
		//$processor = Mage::getSingleton('enterprise_pagecache/processor');
        //$cacheId = $processor->getRequestCacheId() . '_current_product_id';
            
		$blockClass = $this->_placeholder->getAttribute('block');
		$template = $this->_placeholder->getAttribute('template');
		
		$productId = $this->_placeholder->getAttribute('product_id');
		$product = Mage::getModel('catalog/product')->load($productId);
		if (!Mage::registry('product')) {
			Mage::register('product', $product);
		}
		
		$block = new $blockClass;
		$block->setTemplate($template);
        $block->setLayout(Mage::app()->getLayout());
        
		return $block->toHtml();
	}
        
	
    protected function _saveCache($data, $id, $tags = array(), $lifetime = 86400)
    {
        return false;
    }
}