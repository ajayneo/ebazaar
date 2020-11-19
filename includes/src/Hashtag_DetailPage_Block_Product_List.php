<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of List
 *
 * @author Sonia Jain
 */
class Hashtag_DetailPage_Block_Product_List extends Mage_Tag_Block_Product_List {
    
   public function renderTags($pattern, $glue = ' ')
    {
        $out = array();
        foreach ($this->getTags() as $tag) {
            $out[] = sprintf($pattern,
                //$tag->getTaggedProductsUrl().$this->escapeHtml(str_replace(" ","-",$tag->getTagPageTitle())), $this->escapeHtml($tag->getName()), $tag->getProducts()
                $tag->getTaggedProductsUrl(), $this->escapeHtml($tag->getName()), $tag->getProducts()
            );
        }        
        return implode($out, $glue);
    }


}
