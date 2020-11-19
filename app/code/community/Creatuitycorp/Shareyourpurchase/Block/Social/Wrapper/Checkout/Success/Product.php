<?php

class Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Checkout_Success_Product extends Mage_Core_Block_Template {
    
    protected function _toHtml() {
         $block = Mage::app()
                 ->getLayout()
                 ->createBlock('shareyourpurchase/social_wrapper_checkout_success_product_buttons')
                 ->setTemplate('shareyourpurchase/product_buttons.phtml');
         $block->addFacebookButton();
         $block->addTwitterButton();
         
         if ($this->getProduct() && $this->getProduct()->getId()) {
            $block->addPinterestButton();
         }
         
         $this->setChild('social.products.buttons', $block);
         return parent::_toHtml();
    }
}
