<?php 
class Neo_Vowdelight_Block_Adminhtml_Vowdelight_Renderer_Rvp extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{ 
  public function render(Varien_Object $row) 
  { 
        $value =  $row->getData();
        $rvp_link = '';
        if($value['rvp_awb_no'] == '')
        {
          $rvp_link = '<a href="'.Mage::helper('adminhtml')->getUrl('*/*/reversePickup', array("id" => $row->getRequestId())).'">RVP</a>';
        }
        return $rvp_link;
  } 
}
?>