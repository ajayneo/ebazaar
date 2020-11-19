<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CronJob_DisabledBanner_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $tomorrow = date("Y-m-d",strtotime("+1 days"));
        #$datetime = new DateTime('tomorrow');
        #echo $datetime->format('Y-m-d');
        $banners = Mage::getModel('bannerslider/banner')->getCollection()
            //->addFieldToFilter('bannerslider_id', $block_data->getId())
            ->addFieldToFilter('status', 0)                   
            //->addFieldToFilter('start_time', array('lteq' => $today))
            ->addFieldToFilter('end_time', array('like' => "$tomorrow%"));
           //->setOrder('order_banner', "ASC");
        $result = array();
        foreach ($banners as $banner){
                $result[] = $banner->getData();
        }
        
        if(!empty($result)) {
            $this->_sendMail($result);
        }
    }
    
    protected function _sendMail($data = FALSE)
    {
        if($data) {
            // Gets the current store's id
            $storeId = Mage::app()->getStore()->getStoreId();
            $banner_name = array();
            foreach($data as $banner) {
                $banner_name[] = $banner['name'];
            }
            $banner_cs = implode(',', $banner_name);
            $tomorrow = date("Y-m-d",strtotime("+1 days"));
            $date_readable = date("F jS, Y",strtotime($tomorrow));
            if($banner_cs) {
                $templateId = "Banner Disabled";
                $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
                $vars = array('banners' => $banner_cs, 'expiry_date' => $date_readable);
                $emailTemplate->getProcessedTemplate($vars);
                $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
                $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
                
                $emailids = trim(Mage::getStoreConfig('bannerdisabled_section/bannerdisabled_group/bannerdisabled_field'));
                if(!empty($emailids)) {
                    $emailids_array = explode(',', $emailids);
                    #foreach ($emailids_array as $emailid) {
                    #    $emailid = trim($emailid);
                    #    if(!empty($emailid)) {
                            if (!$emailTemplate->send($emailids_array,'', $vars)) {
                                throw new Exception();
                            }
                    #    }
                    #}
                }
                #echo 'Mail Send Successfully...';die;
            }
        }
    }
}
