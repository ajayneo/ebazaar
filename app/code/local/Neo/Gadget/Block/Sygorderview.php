<?php
class Neo_Gadget_Block_Sygorderview extends Mage_Core_Block_Template{ 

    public function getOrderData()
    {       
        $orderid = '';
        $orderid = $this->getRequest()->getParams('id');        
        if(!empty($orderid))
        {   
            if(isset($orderid['id']))
            {
            $orderdata = Mage::getModel('gadget/request')->getCollection()
                                    ->addFieldToFilter('id',$orderid['id']);
                                    //->addFieldToFilter('cid',$cid);
            $orderdata = $orderdata->getData();//print_r($orderdata);
            return $orderdata;
            }
        }                 
    }

     /*
    * @date : 16th Mar 2018
    * @author : Sonali Kosrabe 
    * @purpose : To get customer all addresses dropdowmn array
    */

    public function getCustomerAddress($addressId = NULL){
        
            $address = Mage::getModel('customer/address')->load($addressId);
            return $address->getFirstname().' '.$address->getLastname().', '.implode(',',$address->getStreet()).'<br>'.$address->getPostcode().' '.$address->getCity().'<br>'.$address->getRegion().', '.$address->getCountry();
        
        

    }
}

?>