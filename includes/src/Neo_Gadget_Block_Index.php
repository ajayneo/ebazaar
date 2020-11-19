<?php   
class Neo_Gadget_Block_Index extends Mage_Core_Block_Template{   


	public function getProductCollection($brand)
	{
		$productCollection = Mage::getModel("gadget/gadget")->getCollection()->addFieldToFilter('brand',$brand)->getData();
		return $productCollection;
	}


	public function getProduct($id)
	{
		$product = Mage::getModel("gadget/gadget")->load($id)->getData();
		return $product;
	}


}