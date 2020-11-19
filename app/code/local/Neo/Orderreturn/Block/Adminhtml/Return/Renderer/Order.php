 <?php
 class Neo_Orderreturn_Block_Adminhtml_Return_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{

		$order_number =  $row->getValue();


		$imeiList = $this->helper('orderreturn')->getImeiList($order_number);
		

		$table_str = '';
		$table_str .="<table><tr><th colspan=\"2\">IMEI</th><th colspan=\"2\">Reason</th><th colspan=\"2\">Return Action</th colspan=\"2\"><th colspan=\"2\">Status</th></tr>";

		foreach ($imeiList['imei_list'] as $key => $value) {
			$table_str .="<tr><td colspan=\"2\">".$key."</td>";
			$table_str .="<td colspan=\"2\">".$value['reason']."</td>";
			$table_str .="<td colspan=\"2\">".$value['return_action']."</td>";
			$table_str .="<td colspan=\"2\">".$value['status']."</td>";
			$table_str .="</tr>";
			$count++;
		}
		$table_str .="</table>";
        return $table_str;
	}
 
} 