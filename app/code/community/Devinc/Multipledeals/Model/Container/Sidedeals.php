<?php
/**
* Placeholder container for homepage special products block
*
* @category    Mymodule
* @package     Oggetto_Mymodule
*/
class Devinc_Multipledeals_Model_Container_Sidedeals extends Enterprise_PageCache_Model_Container_Abstract
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
		return 'MULTIPLEDEALS_SIDEDEALS' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
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