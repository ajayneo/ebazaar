<?php
class Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Form_Element_Label extends Varien_Data_Form_Element_Label
{
    public function getElementHtml()
    {
    	$html = $this->getBeforeElementHtml();
    	$html .= $this->getHiddenFieldHtml();
    	$html .= $this->getBold() ? '<strong>' : '';
    	$html.= "<span id=\"{$this->getHtmlId()}\">";
	    	if ($this->getUrl()) $html .= '<a href="' . $this->getUrl() . '">';
	    	$html .= $this->getEscapedValue();
	    	if ($this->getUrl()) $html .= '</a>';
    	$html .= "</span>";
    	$html.= $this->getBold() ? '</strong>' : '';
    	$html.= $this->getAfterElementHtml();
    	return $html;
    }

    public function getHiddenFieldHtml() {
    	if ($this->getHiddenField()) {
    		return $this->getHiddenField()->getElementHtml();
    	}
        return null;
    }
}