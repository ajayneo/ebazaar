<?php
class Neo_Customblocks_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getIsProductInWishlist($id){
		$hasProduct = 0;
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
			$collection = Mage::getModel('wishlist/item')->getCollection()->addFieldToFilter('wishlist_id', $wishlist->getId())->addFieldToFilter('product_id', $id);
			$item = $collection->getFirstItem();
			$hasProduct = $item->getWishlistItemId();
		}		
		return $hasProduct;
	}
}
	 