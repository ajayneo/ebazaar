<?php
/**
 * Neo_Notification extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Neo
 * @package        Neo_Notification
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Notification edit form tab
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Block_Adminhtml_Notification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('enctype' => 'multipart/form-data'));
        $form->setHtmlIdPrefix('notification_');
        $form->setFieldNameSuffix('notification');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'notification_form',
            array('legend' => Mage::helper('neo_notification')->__('Notification'))
        );

        $notification_type = $fieldset->addField('notfication_type', 'select', array(
            'label'     => Mage::helper('neo_notification')->__('Notification Type'),
            'name'      => 'notfication_type',
            'required' => true,
            'values'    => array(
                '1' => 'Text',
                '2'   => 'Image',
                '3'   => 'Link',
                '4'   => 'App Update'
            )
        ));

        // $notification_type = $fieldset->addField(
        //     'notification_type',
        //     'select',
        //     array(
        //         'label'  => Mage::helper('neo_notification')->__('Notification Type'),
        //         'name'   => 'notification_type',
        //         'values' => array(
        //             array(
        //                 'value' => 2,
        //                 'label' => Mage::helper('neo_notification')->__('Notification Sent'),
        //             ),
        //             array(
        //                 'value' => 1,
        //                 'label' => Mage::helper('neo_notification')->__('Enabled'),
        //             ),
        //             array(
        //                 'value' => 0,
        //                 'label' => Mage::helper('neo_notification')->__('Disabled'),
        //             ),
        //         ),
        //     )
        // );

        $title = $fieldset->addField('title','text',array(
            'label' => Mage::helper('neo_notification')->__('Title'),
            'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',
           )
        );

        $banner_image = $fieldset->addField('image_name','image',array(
            'label' => Mage::helper('neo_notification')->__('Image'),
            'required' => true,
            'name' => 'image_name',
            )
        );

        $image_link_type = $fieldset->addField('image_link_type', 'select', array(
            'label'     => Mage::helper('neo_notification')->__('Select Link Type'),
            'name'      => 'image_link_type',
            'required' => false,
            'values'    => array(
                //''  => '',
                '1' => 'Category',
                '2' => 'Product'
            )
        ));
        
        $sku = $fieldset->addField('sku','text',array(
            'label' => Mage::helper('neo_notification')->__('Search SKU'),
            'name'  => 'sku',
            'required'  => false,
            'after_element_html' => '<button type="button" onclick="searchproduct()">Search SKU</button>'
            )
        );

        $product_id = $fieldset->addField('product_id','select',array(
            'label' => Mage::helper('neo_notification')->__('Product'),
            'name'  => 'product_id',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> Mage::getModel('neo_notification/notification_attribute_source_productid')->getAllOptions(true),
            )
        );

        $category_id = $fieldset->addField('category_id','select',array(
            'label' => Mage::helper('neo_notification')->__('Category'),
            'name'  => 'category_id',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> Mage::getModel('neo_notification/notification_attribute_source_categoryid')->getAllOptions(true),
           )
        );

        $link = $fieldset->addField('link_url','text',array(
            'label' => Mage::helper('neo_notification')->__('Link Url'),
            'name'  => 'link_url',
            'required'  => false,
            //'class' => 'required-entry',
            )
        );

        $status = $fieldset->addField('status','select',array(
            'label'  => Mage::helper('neo_notification')->__('Status'),
            'name'   => 'status',
            'values' => array(
                array(
                    'value' => 5,
                    'label' => Mage::helper('neo_notification')->__('Sent Test Notification'), 
                ),
                array(
                    'value' => 4,
                    'label' => Mage::helper('neo_notification')->__('Notification Sent'),
                ),
                array(
                    'value' => 3,
                    'label' => Mage::helper('neo_notification')->__('Add Notification to Cron'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('neo_notification')->__('Send Notification Immediately'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('neo_notification')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('neo_notification')->__('Disabled'),
                ),
            ),
            )
        );
        $formValues = Mage::registry('current_notification')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getNotificationData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getNotificationData());
            Mage::getSingleton('adminhtml/session')->setNotificationData(null);
        } elseif (Mage::registry('current_notification')) {
            $formValues = array_merge($formValues, Mage::registry('current_notification')->getData());
        }
        $form->setValues($formValues);

        $this->setForm($form);
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($notification_type->getHtmlId(), $notification_type->getName())
            ->addFieldMap($image_link_type->getHtmlId(), $image_link_type->getName())
            ->addFieldMap($banner_image->getHtmlId(), $banner_image->getName())
            ->addFieldMap($product_id->getHtmlId(), $product_id->getName())
            ->addFieldMap($sku->getHtmlId(), $sku->getName())
            ->addFieldMap($category_id->getHtmlId(), $category_id->getName())
            ->addFieldMap($link->getHtmlId(), $link->getName())
            ->addFieldDependence(
                $product_id->getName(),
                $notification_type->getName(),
                array('1','2')
            )
            ->addFieldDependence(
                $banner_image->getName(),
                $notification_type->getName(),
                '2'
            )
            ->addFieldDependence(
                $image_link_type->getName(),
                $notification_type->getName(),
                array('1','2')
            )
            ->addFieldDependence(
                $category_id->getName(),
                $notification_type->getName(),
                array('1','2')
            )
            ->addFieldDependence(
                $link->getName(),
                $notification_type->getName(),
                '3'
            )
            ->addFieldDependence(
                $category_id->getName(),
                $image_link_type->getName(),
                '1'
            )
            ->addFieldDependence(
                $product_id->getName(),
                $image_link_type->getName(),
                '2'
            )            
            ->addFieldDependence(
                $sku->getName(),
                $image_link_type->getName(),
                '2'
            )
            /*->addFieldDependence(
                $link->getName(),
                $notification_type->getName(),
                '4'
            )*/
        );

        return parent::_prepareForm();
    }
}
?>
<script type="text/javascript">
    function searchproduct(){
        var sku = jQuery("#notification_sku").val();

        if(sku == ''){
            alert("Enter SKU code for searching");
            return false;
        }
        
        jQuery.ajax({
            url: '<?php echo Mage::helper('adminhtml')->getUrl("*/*/searchproduct") ?>', 
            data: {SKU: sku},
            beforeSend: function(){
                jQuery("#loading-mask").show();
            },
            success: function (data) {
                var obj = jQuery.parseJSON(data);
                if(obj.status == 1){
                    jQuery("#notification_product_id").val(obj.product_id);
                }else{
                    alert(obj.message);
                    return false;
                }
            },
            complete: function(){
                jQuery("#loading-mask").hide();
            }

        });
    }
</script>