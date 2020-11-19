<?php
class Neo_Newregistrationcoupon_Model_Observer
{
    const XML_PATH_EMAIL_RECIPIENT  = 'newregistrationcoupon/newregistrationcoupon/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'newregistrationcoupon/newregistrationcoupon/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'newregistrationcoupon/newregistrationcoupon/email_template';


    public function customerSaveAfter(Varien_Event_Observer $o)
    {
        //echo Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE);exit;
        //Array of customer data
        $customerData = $o->getCustomer()->getData();
        //This coupon is deactivated
        /*
        if (!$o->getCustomer()->getOrigData()) {


            $post['name'] = $customerData['firstname'] . ' ' . $customerData['lastname'];
            $post['email'] = $customerData['email'];
            $post['code']  = 'NEWSIGNUP200'; 
            //customer is new, otherwise it's an edit          
            
            $translate = Mage::getSingleton('core/translate'); 
            $translate->setTranslateInline(false);

            $postObject = new Varien_Object();
            $postObject->setData($post);

            $mailTemplate = Mage::getModel('core/email_template');
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->setReplyTo(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    $post['name'],
                    null, 
                    array('data' => $postObject)
                );

            $translate->setTranslateInline(true);    

        }*/
    }
        
}
