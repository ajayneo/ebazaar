<?php 
class Hashtag_DetailPage_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('addtags/index/index'); //
        $parameter_url =$this->getUrl("addtags/index/getvalues");
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Generate Tags')
               // ->setOnClick("call gotoaddTag('$url')")
             //    ->setOnClick("location.href='$url'")
                    ->toHtml();
        $html.='<br/><div id="error" style="display:none" class="validation-advice">Please select attribute first from above list</div><script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script><script type="text/javascript">
       jQuery = $.noConflict();
       jQuery(document).ready(function(){
       jQuery(".scalable").click(function(){
       var selectedElem = jQuery("#hashtag_hashtag_group_attribute").val();
       var selectedCat = jQuery("#hashtag_hashtag_group_category_name").val();
         var urls = "'.$parameter_url.'";
             var value_data = Array();
             jQuery.post(urls,{"attributes":selectedElem,"form_key":"'.Mage::getSingleton('core/session')->getFormKey().'"},function(data){
                    var value_data = JSON.parse(data);
                    if(value_data!="")
                    {
                            var len = value_data.length;
                            console.log("lenght="+len);    
                            showQueue(selectedElem,value_data,len,selectedCat);
    
                    }
                  
       });
       
     });
     
     function showQueue(attribute_name,selectedElem1,lengths,cat=4)
     {
        var presentElem = selectedElem1.pop();
        var url = "'.$url.'";
             jQuery.post(url,{"category_id":cat,"attribute":attribute_name,"key":presentElem.label,"value":presentElem.value,"form_key":"'.Mage::getSingleton('core/session')->getFormKey().'"},function(data){
                   if(data!="")
                   {
                    jQuery(".response").append("<div>"+data+"</div>");
                    lengths = lengths-1;
                    if(lengths!=0)
                    showQueue(attribute_name,selectedElem1,lengths,cat);
                    }
       });
           }
     });
     
    </script><style> .response div{ margin-top:3px;background: none repeat scroll 0 0 green;color: white; } </style><div class="response" style="padding-top:5px;"></div>';
        
        
        return $html;
    }
}
?>