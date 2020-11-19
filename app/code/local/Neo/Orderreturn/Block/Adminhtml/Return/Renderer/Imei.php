 <?php
 class Neo_Orderreturn_Block_Adminhtml_Return_Renderer_Imei extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{

		$value =  $row->getValue();
		$newval = explode(',', rtrim($value,","));
		
		//$out = $value;
        //$out = "<img src=". $media . $value ." width='40px'/>";

        $out = "<table>";
        $out .= "<tr><th>IMEI</th></tr>";
        foreach ($newval as $key => $imei) {
	        $out .= "<tr><td>".$imei."</td></tr>";
        }
        $out .= "</table>";
        return $out;
	}
 
} 