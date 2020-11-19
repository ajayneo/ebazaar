<?php
/**
* Placeholder container for homepage special products block
*
* @category    Mymodule
* @package     Oggetto_Mymodule
*/
class Devinc_Multipledeals_Model_Container_List extends Enterprise_PageCache_Model_Container_Abstract
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
        return 'CATALOG_LIST' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    /**
    * Render block content
    *
    * @return string
    */
	protected function _renderBlock()
	{
		$blockClass = $this->_placeholder->getAttribute('block');
		$template = $this->_placeholder->getAttribute('template');
		$categoryId = $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CATEGORY_ID, '');		
		
		//$product = Mage::getModel('catalog/product');
		//Mage::register('product', $product);
		
		$category = Mage::getSingleton('catalog/category')->load($categoryId);
		Mage::register('current_category', $category);
		
		$layer = Mage::getSingleton('catalog/layer');
		Mage::register('current_layer', $layer);
		
		$block = new $blockClass;
		$block->setTemplate($template);
        $block->setCategoryId($categoryId);
        $block->setLayout(Mage::app()->getLayout());
        
		return $block->toHtml();
	}
        	
    protected function _saveCache($data, $id, $tags = array(), $lifetime = 86400)
    {
        return false;
    }
}