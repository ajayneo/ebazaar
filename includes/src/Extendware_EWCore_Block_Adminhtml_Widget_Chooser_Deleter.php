<?php

class Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Deleter extends Mage_Adminhtml_Block_Widget_Button
{
	public function __construct($arguments=array())
    {
        parent::__construct($arguments);
    }
    
    protected function _beforeToHtml()
    {
        $this->setOnClick($this->getJsObjectName() . 'Click();')
            ->setType('button')
            ->setClass('delete icon-btn chooser-btn')
            ->setStyle('margin-left: 3px;')
            ->setLabel(Mage::helper('adminhtml')->__('Delete'));
        return parent::_beforeToHtml();
    }

	protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html .= Mage::helper('adminhtml/js')->getScript("
        	function {$this->getJsObjectName()}Click() {
        		window." . $this->getSelector()->getUniqueId() . ".setElementValue('');
        		window." . $this->getSelector()->getUniqueId() . ".setElementLabel(" . json_encode($this->getWidget()->getDefaultText()) . ");
        		window." . $this->getSelector()->getUniqueId() . ".triggerCallback();
    		}
        ");

        return $html;
    }
    
    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
}
