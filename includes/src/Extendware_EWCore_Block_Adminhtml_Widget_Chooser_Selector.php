<?php

class Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Selector extends Mage_Adminhtml_Block_Widget_Button
{
	public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setId('chooser');
        $this->setUniqueId('id' . md5(uniqid('', true)));
    }
    
    public function setId($value) {
    	$part = str_replace('_chooser', '', $value);
    	if ($value != 'chooser') {
	    	if (!$this->getLabelElementId()) {
	    		$this->setLabelElementId($part . '_label');
	    	}
	    	
	    	if (!$this->getValueElementId()) {
	    		$this->setValueElementId($part . '_id');
	    	}
    	}
    	return parent::setId($value);
    }
    
    protected function _beforeToHtml()
    {
        $this->setOnClick($this->getJsObjectName() . 'Click();')
            ->setType('button')
            ->setClass('add');
        if ($this->hasLabel() === false) {
            $this->setLabel(Mage::helper('ewcore')->__('Edit'));
        }

        if ($this->getCanDelete() === true) {
	        $block = $this->getLayout()->createBlock('ewcore/adminhtml_widget_chooser_deleter');
	        $block->setId($this->getId() . '_deleter');
	        $block->setSelector($this);
	        $block->setWidget($this->getWidget());
	        $this->setAfterHtml($block->toHtml());
        }
        
        return parent::_beforeToHtml();
    }
	
    protected function getRoute() {
    	$controller = $this->_camelize($this->getId());
    	$controller{0} = strtolower($controller{0});
    	return '*/*/' . $controller;
    }
    
    protected function _toHtml()
    {
    	$uniqueId = $this->getUniqueId();
    	$urlParams = is_array($this->getUrlParams()) ? $this->getUrlParams() : array();
    	$urlParams = array_merge($urlParams, array('uniq_id' => $uniqueId, 'isAjax' => true));
    	$url = $this->getUrl($this->getRoute(), $urlParams);
    	
        $html = parent::_toHtml();
        $html .= Mage::helper('adminhtml/js')->getScript("
        	var {$this->getJsObjectName()}Dialog = widgetTools;
            function {$this->getJsObjectName()}Click() {
            	{$this->getJsObjectName()}Dialog.openDialog('{$url}');
        	}
        	
        	
        	{$uniqueId} = {};
        	{$uniqueId}.setElementValue = function(itemId) { 
    			$$('#{$this->getValueElementId()}', 'input:hidden [id={$this->getValueElementId()}]').each(function(s) {
					s.value = itemId;
				});
    		};
    		
        	{$uniqueId}.setElementLabel = function(itemName) { 
				$$('#{$this->getLabelElementId()}').each(function(s) {
					if (typeof s.innerText != 'undefined') {
						s.innerText = itemName;
					} else {
						s.textContent = itemName;
					}
				});
    		};
    		
    		{$uniqueId}.setElementUrl = function(itemUrl) { 
				$$('#{$this->getLabelElementId()}').each(function(s) {
					if (itemUrl) {
						var url = s.down('a');
						if (!url) {
							var html = s.innerHTML;
							var a = new Element('a');
							a.href = itemUrl;
							a.innerHTML = html;
							s.update(a);
						}
					}
				});
    		};
    		
    		{$uniqueId}.triggerCallback = function(params) {
				" . ($this->getJavascriptCallback() ? $this->getJavascriptCallback() . '(params);' : '') . "
    		};
    		
        	{$uniqueId}.close = function() { {$this->getJsObjectName()}Dialog.closeDialog(); };
        ");

        return $html;
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
}
