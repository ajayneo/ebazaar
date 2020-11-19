<?php
class Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Form_Element_Widget extends Varien_Data_Form_Element_Label
{
	public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('chooser_widget');
    	if ($this->hasShowButton() === false) {
        	$this->setShowButton(true);
        }
        
        $this->process();
    }
    
    protected function process() {
    	$block = $this->getSelectorButton();
        
        $afterHtml = '';
        if ($this->getShowButton() === true) {
			$afterHtml = '<span style="padding-left: 15px">' . $block->toHtml() . '</span>';
        }
		
        $hiddenField = $this->getFieldset()->addField($block->getValueElementId(), 'text', array(
        	'name'      => $block->getValueElementId(),
        	'index'		=> $block->getValueElementId(),
        	'value' 	=> $this->getValue(),
        	'class'		=> $this->getRequired() ? 'required-entry' : '',
        	'style' 	=> 'display: none',
        ));
        $this->getFieldset()->removeField($block->getValueElementId());
        
        $this->getFieldset()->addType('chooser_widget_label', 'Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Form_Element_Label');
        $this->getFieldset()->addField($block->getLabelElementId(), 'chooser_widget_label', array(
        	'name'      => $block->getLabelElementId(),
        	'hidden_field' => $hiddenField,
            'label'     => $this->getLabel(),
        	'url'		=> $this->getUrl(),
        	'value' 	=> $this->getText() !== null ? $this->getText() : $this->getDefaultText(),
        	'after_element_html' => $afterHtml,
        	'bold' 		=> $this->getBold(),
        	'required'	=> $this->getRequired(),
        	'note'		=> $this->getNote(),
        	'ewhelp'	=> $this->getEwhelp()
        ));
        
        return $this;
    }
    protected function getSelectorButton() {
        $block = $this->getLayout()->createBlock('ewcore/adminhtml_widget_chooser_selector');
		$block->setId($this->getData('name'));
		$block->setWidget($this);
		$block->setLabel($this->getButtonLabel());
		$block->setJavascriptCallback($this->getJavascriptCallback());
        $block->setCanDelete($this->getCanDelete());
        if (is_array($this->getData('url_params'))) {
        	$block->setUrlParams($this->getData('url_params'));
        }
        if ($this->getLabelElementId()) $block->setLabelElementId($this->getLabelElementId());
        if ($this->getValueElementId()) $block->setValueElementId($this->getValueElementId());
        
        
        return $block;
    }
    
    public function getDefaultText() {
    	if ($this->hasDefaultText() === true) {
    		return $this->getData('default_text');
    	}
    	
    	return '[' . Mage::helper('ewcore')->__('none') . ']';
    }
    
    public function getHtml() {
    	return null;
    }
}