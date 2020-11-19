<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Result
 *
 * @author Sonia Jain
 */
class Hashtag_DetailPage_Block_Product_Result extends Mage_Tag_Block_Product_Result {
     
    public function getHeaderText()
    {
        if( $this->getTag()->getName() ) {
            return Mage::helper('tag')->__("%s", $this->escapeHtml($this->getTag()->getData("tag_page_title")));
        } else {
            return false;
        }
    }

    protected function _prepareLayout()
    {
        $title = lcfirst($this->getTag()->getData("tag_page_title"));
        $description = $this->getTag()->getData("tag_description");
        $keywords = $this->getTag()->getData("tag_keywords"); 
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock("head")->setKeywords($keywords);
        $this->getLayout()->getBlock("head")->setDescription($description);
        //echo $this->getLayout()->getBlock("head")->getKeywords();
       // $this->getLayout()->getBlock('root')->setHeaderTitle($title);
       // return parent::_prepareLayout();
    }
}