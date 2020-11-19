<?php

class Neo_Gadget_Block_Adminhtml_Request_Renderer_Optionform extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		//$media = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$value =  $row->getValue();
		
        $table3 = str_replace('color:#ffffff', 'color:#000', $value); 
        $table2 = str_replace('tr', 'ul', $table3); 
        $table = str_replace('td', 'li', $table2); 

        $paramId = Mage::app()->getRequest()->getParam('id');
        $image = Mage::getModel("gadget/request")->load($paramId)->getImage();

        $img = '<img src="'.$image.'"/>';

		return '<style> 
			table, th, td {
			    border: 2px solid black;
			    border-collapse: collapse;
			}
		</style>
		<table style="width:100%;"> 
			<tr>
				<th style="width:30%;padding:5px"><h2>ATTRIBUTES SELECTED</h2></th>
				<th style="width:70%;padding:5px"><h2>IMAGE</h2></th>
			</tr>
			<tr>
				<td style="width:30%;padding:15px">'.$table.'</td>
				<td style="width:70%;padding:5px" align="center">'.$img.'</td>
			</tr>

		</table>';   
 
        //return $out;
	}
 
} 
      