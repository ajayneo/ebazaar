<?php
class Neo_Ebpin_Helper_Data extends Mage_Core_Helper_Abstract
{
	//generate 4 digit random number
	public function generateOTP()
    {
        $length = 4;
        $chars = '123456789';
        $chars_length = (strlen($chars) - 1);
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < $length; $i = strlen($string)) {
            $r = $chars{rand(0, $chars_length)};
            if ($r != $string{$i - 1}) $string .= $r;
        }

        return $string;
    }

    //add otp

    public function addOTP($customer_mobile)
    {
        try {
            $otp_data = array();
            $otp_code = $this->generateOTP();

            if ($otp_code) {
                $otp_data = array($customer_mobile => $otp_code);
                Mage::getSingleton('core/session')->setEBPin($otp_data);
                $count = '';
                $count = Mage::getSingleton('core/session')->getCountEBPin();
                Mage::log('OTP Count Before :'.$count, null, 'order-confirm.log', true);                
                if($count >= 0)
                {
                  $count++;  
                  Mage::getSingleton('core/session')->setCountEBPin($count); //added by JP for order confirm.
                }
                else
                {
                  $count = 0;
                  Mage::getSingleton('core/session')->setCountEBPin($count);
                }
                Mage::log('OTP Count After :'.$count, null, 'order-confirm.log', true);
            }
            return $otp_code;
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

    }
}
	 