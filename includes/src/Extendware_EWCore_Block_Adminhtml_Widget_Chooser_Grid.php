<?php
class Extendware_EWCore_Block_Adminhtml_Widget_Chooser_Grid extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid
{
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        
        $this->setItemLabelJs('trElement.down("td").next().innerHTML');
    	$this->setItemValueJs('trElement.down("td").innerHTML.stripTags()');
    	$this->setItemUrlJs('(trElement.down("td").down("input") ? trElement.down("td").down("input").value : null)');
    	
    	$columnRenderers = $this->getColumnRenderers();
    	if (is_array($columnRenderers) === false) $columnRenderers = array();
    	$columnRenderers['chooser_textwithurl'] = 'ewcore/adminhtml_widget_chooser_grid_renderer_textwithurl';
    	$this->setColumnRenderers($columnRenderers);
    }

    public function getRowClickCallback()
    {
		$chooserJsObject = $this->getId();
		return '
			function (grid, event) {
				var trElement = Event.findElement(event, "tr");
				var itemValue = ' . $this->getItemValueJs() . ';
				var itemLabel = ' . $this->getItemLabelJs() . ';
				var itemUrl = ' . $this->getItemUrlJs() . ';
				var optionLabel = itemLabel.trim();
				var optionValue = itemValue.trim();
				var optionUrl = itemUrl;'.
				$chooserJsObject.'.setElementValue(optionValue);'.
				$chooserJsObject.'.setElementLabel(optionLabel);'.
				$chooserJsObject.'.setElementUrl(optionUrl);'.
				$chooserJsObject.'.triggerCallback({label: optionLabel, value: optionValue, url: optionUrl});'.
				$chooserJsObject.'.close();
			}
		';
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current' => true,
            'uniq_id' => $this->getId(),
        	'use_massaction' => $this->getUseMassaction()
        ));
    }
}
