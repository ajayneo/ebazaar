<?php
class Extendware_EWCore_Block_Adminhtml_Widget_Grid_Column_Filter_Range_Compact extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Range
{
    public function getHtml()
    {
        $html = '<div class="range"><div class="range-line" style="width: 75px !important"><span class="label">' . Mage::helper('adminhtml')->__('From').':</span> <input type="text" name="'.$this->_getHtmlName().'[from]" id="'.$this->_getHtmlId().'_from" value="'.$this->getEscapedValue('from').'" class="input-text no-changes" style="width: 30px !important"/></div>';
        $html .= '<div class="range-line" style="width: 75px !important"><span class="label">' . Mage::helper('adminhtml')->__('To').' : </span><input type="text" name="'.$this->_getHtmlName().'[to]" id="'.$this->_getHtmlId().'_to" value="'.$this->getEscapedValue('to').'" class="input-text no-changes" style="width: 30px !important"/></div></div>';
        return $html;
    }
}
