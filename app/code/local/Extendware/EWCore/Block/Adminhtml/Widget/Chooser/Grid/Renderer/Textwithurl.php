<?php

class Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Grid_Renderer_Textwithurl extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid_Column_Renderer_Text
{
	public function render(Varien_Object $row)
    {
    	$html = parent::render($row);
    	$href = '';
    	$url = $this->getColumn()->getData('url');
    	if (is_array($url) === true and isset($url['route'])) {
    		$params = isset($url['params']) ? $url['params'] : array();
    		if (is_array($params) === false) $params = array();
    		
    		if (isset($url['row_data_params']) and is_array($url['row_data_params']) === true) {
    			foreach ($url['row_data_params'] as $key => $value) {
    				$params[$key] = $row->getData($value);
    			}
    		}
    		
    		$href = $this->getUrl($url['route'], $params);
    	}
    	
		$name = $this->getColumn()->getId() . '_url';
		$html .= '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $href . '">';
        return $html;
    }
}
