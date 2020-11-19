<?php

class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success_Product_List extends Mage_Core_Block_Text_List {
    
    public function createProductBlocks() {
        $items = $this->getProducts();
        foreach ($items as $item) {
            $this->_appendProductBlock($item);
        }
    }
    
    protected function _appendProductBlock($item) {
        $block = Mage::app()
                ->getLayout()
                ->createBlock('shareyourpurchase/social_wrapper_checkout_success_product')
                ->setTemplate('shareyourpurchase/product.phtml');
        $block->setItem($item);
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $block->setProduct($product);
        $this->append($block);
    }
    
}
