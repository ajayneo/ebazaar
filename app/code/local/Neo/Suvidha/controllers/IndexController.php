<?php
class Neo_Suvidha_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      if(Mage::getSingleton('customer/session')->isLoggedIn()) {
          $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Credit Program"));
              $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
            $breadcrumbs->addCrumb("home", array(
                    "label" => $this->__("Home Page"),
                    "title" => $this->__("Home Page"),
                    "link"  => Mage::getBaseUrl()
                ));

            $breadcrumbs->addCrumb("credit program", array(
                    "label" => $this->__("Credit Program"),
                    "title" => $this->__("Credit Program")
                ));

        $this->renderLayout(); 
    }else{
            // code added to make customer logged in on click of shubh labh from android        
            if($this->getRequest()->getParam('cemail') && $this->getRequest()->getParam('cemail') != ''){
                // echo "string"; exit;
                $customer = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($this->getRequest()->getParam('cemail'));

                if ($customer->getId()) {
                    $session = $session = Mage::getSingleton('customer/session');
                    $session->setCustomerAsLoggedIn($customer);
                    Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl().'shubhlabh/');
                }
            }else{
                Mage::getSingleton('customer/session')->setAfterAuthUrl(Mage::helper('core/url')->getCurrentUrl());
                Mage::getSingleton('core/session')->addError('An instant, hassel free credit scheme for all retailers to ensure seamless business growth. Please login to avail Shubh Labh Credit Scheme.');
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'))->sendResponse();
            }

        }
    }

    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();
        // var_dump($post_data);exit;
        // print_r($post_data);exit;
        $ifexist = Mage::getModel('suvidha/creditsuvidha')->getCollection()->addFieldToFilter('email_id', $post_data['email_id'])->getData(); 
        $suvidhaCust = $ifexist[0];

        $entity_id = (int) $suvidhaCust['id'];

        if($entity_id > 0){ 
        try{       
            $files_array = array(); 
            $customer = array();    
            $customer['name'] = $post_data['customer_name'];
            $customer['mobile'] = $post_data['mobile'];
            $customer['email'] = $post_data['email_id'];
            $modelS = Mage::getModel('suvidha/creditsuvidha')->load($entity_id);

            $uploaddir = Mage::getBaseDir(). DS . 'kaw17842knlpREGdasp9045' . DS . 'suvidha' . DS .'creditsuvidha'. DS;
            $uploadurl = Mage::getBaseUrl() . 'kaw17842knlpREGdasp9045/suvidha/creditsuvidha/' . $entity_id .'/';

                if(!is_dir($uploaddir.$entity_id)){
                    mkdir($uploaddir.$entity_id, 0777, true);
                }

                $uploaddir = $uploaddir . $entity_id .DS;
                // echo "string".$uploaddir;exit;
                // echo $_FILES['aadhar']['name']; 

                if($_FILES['aadhar']['name'] != ''){
                    $uploadfile1 = $uploaddir . basename($_FILES['aadhar']['name']);
                    
                    if (move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadfile1)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['aadhar']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('aadhar', $uploadurl . basename($_FILES['aadhar']['name']));
                }else{
                    $files_array[] =  substr(strrchr($suvidhaCust['aadhar'], "/"), 1);  
                }

                if($_FILES['pancard']['name'] != ''){
                    $uploadfile2 = $uploaddir . basename($_FILES['pancard']['name']);
                    if (move_uploaded_file($_FILES['pancard']['tmp_name'], $uploadfile2)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['pancard']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('pancard', $uploadurl . basename($_FILES['pancard']['name']));
                }else{
                    $files_array[] =  substr(strrchr($suvidhaCust['pancard'], "/"), 1);  
                }

                if($_FILES['postcheque']['name'] != ''){
                    $uploadfile3 = $uploaddir . basename($_FILES['postcheque']['name']);
                    if (move_uploaded_file($_FILES['postcheque']['tmp_name'], $uploadfile3)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['postcheque']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('postcheque', $uploadurl . basename($_FILES['postcheque']['name']));
                }else{
                    $files_array[] =  substr(strrchr($suvidhaCust['postcheque'], "/"), 1);  
                }

                if($_FILES['bankst']['name'] != ''){
                    $uploadfile4 = $uploaddir . basename($_FILES['bankst']['name']);
                    if (move_uploaded_file($_FILES['bankst']['tmp_name'], $uploadfile4)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['bankst']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('bankst', $uploadurl . basename($_FILES['bankst']['name']));
                }else{
                    $files_array[] =  substr(strrchr($suvidhaCust['bankst'], "/"), 1);  
                }

            $modelS->addData($this->getRequest()->getPost());
            $modelS->save();            
            // $this  
            // print_r($files_array);
            if($modelS['form_status'] == 0){
                $modelS->setData('form_status', 1);
                $modelS->setData('status', 3);
                $modelS->save();
                $files_array[] = Mage::helper('suvidha')->generatePdf($entity_id);

                // print_r($files_array);exit;
                $check_mail = Mage::helper('suvidha')->sendRequestEmail($files_array, $customer, $entity_id, $post_data['arm_email'], $post_data['crm_email']);
            }
            Mage::getSingleton("core/session")->addSuccess('Thank you for submitting your request. We will get back to you shortly.');
          }
          catch(Exception $e){
            Mage::getSingleton("core/session")->addError($e->getMessage());
          }
        }else{
          try{
                $newModel = Mage::getModel('suvidha/creditsuvidha');
                $files_array = array();
                $customer = array();    
                $customer['name'] = $post_data['customer_name'];
                $customer['mobile'] = $post_data['mobile'];
                $customer['email'] = $post_data['email_id'];

                if($_FILES['aadhar']['name'] == '' || $_FILES['pancard']['name'] == '' || $_FILES['postcheque']['name'] == '' || $_FILES['bankst']['name'] == ''){
                    Mage::getSingleton("core/session")->addError('Please upload files for Aadhar, Pancard, Postcheque and Bank statement of last 3 months.');
                    $this->_redirectUrl(Mage::getBaseUrl().'shubhlabh/index');
                    return;
                }

                $newModel->setData($post_data)->save();

                // print_r($newModel->getData('id'));exit;
                $entity_id1 = $newModel->getData('id');

                $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha' . DS;
                $uploadurl = Mage::getBaseUrl() . 'kaw17842knlpREGdasp9045/suvidha/creditsuvidha/' . $entity_id1 .'/';

                if(!is_dir($uploaddir.$entity_id1 )){
                    mkdir($uploaddir.$entity_id1 , 0777, true);
                }
                $uploaddir = $uploaddir . $entity_id1 . DS;

                // $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha'.DS;

                if($_FILES['aadhar']['name'] != ''){
                    $uploadfile1 = $uploaddir . basename($_FILES['aadhar']['name']);
                    if (move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadfile1)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['aadhar']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('aadhar', $uploadurl . basename($_FILES['aadhar']['name']));
                }

                if($_FILES['pancard']['name'] != ''){
                    $uploadfile2 = $uploaddir . basename($_FILES['pancard']['name']);
                    if (move_uploaded_file($_FILES['pancard']['tmp_name'], $uploadfile2)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['pancard']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('pancard', $uploadurl . basename($_FILES['pancard']['name']));
                }

                if($_FILES['postcheque']['name'] != ''){
                    $uploadfile3 = $uploaddir . basename($_FILES['postcheque']['name']);
                    if (move_uploaded_file($_FILES['postcheque']['tmp_name'], $uploadfile3)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['postcheque']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('postcheque', $uploadurl . basename($_FILES['postcheque']['name']));
                }

                if($_FILES['bankst']['name'] != ''){
                    $uploadfile4 = $uploaddir . basename($_FILES['bankst']['name']);
                    if (move_uploaded_file($_FILES['bankst']['tmp_name'], $uploadfile4)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['bankst']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('bankst', $uploadurl . basename($_FILES['bankst']['name']));
                }

            $newModel->setData('form_status', 1);
            $newModel->setData('status', 3);
            $newModel->save();

            $files_array[] = Mage::helper('suvidha')->generatePdf($entity_id1);
            $check_mail = Mage::helper('suvidha')->sendRequestEmail($files_array, $customer, $entity_id1, $post_data['arm_email'], $post_data['crm_email']);
            Mage::getSingleton("core/session")->addSuccess('Thank you for submitting your request. We will get back to you shortly.');
          }
          catch(Exception $e){
            Mage::getSingleton("core/session")->addError($e->getMessage());
          }
        }
        $this->_redirectUrl(Mage::getBaseUrl().'shubhlabh/index');
    }

    public function savedataAction()
    {
        $post_data = $this->getRequest()->getParams();

        $ifexist = Mage::getModel('suvidha/creditsuvidha')->getCollection()->addFieldToFilter('email_id', $post_data['email_id'])->getData(); 
        $suvidhaCust = $ifexist[0];

        // print_r($suvidhaCust);
        $entity_id1 = 0;

        if($suvidhaCust['id']){
            $entity_id1 = (int) $suvidhaCust['id'];
        }

        // echo "string ". $entity_id1;exit;

        $newModel = '';

        if($entity_id1 > 0){ 
            $newModel = Mage::getModel('suvidha/creditsuvidha')->load($entity_id1);
        }else{
            $newModel = Mage::getModel('suvidha/creditsuvidha');
        }

        $newModel->addData($post_data)->save();

        // print_r($newModel->getData('id'));exit;
        $entity_id1 = $newModel->getData('id');

        $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha' . DS;
        $uploadurl = Mage::getBaseUrl() . 'kaw17842knlpREGdasp9045/suvidha/creditsuvidha/' . $entity_id1 .'/';

        if(!is_dir($uploaddir.$entity_id1 )){
            mkdir($uploaddir.$entity_id1 , 0777, true);
        }

        $uploaddir = $uploaddir . $entity_id1 . DS;

        // $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha'.DS;

        if($_FILES['aadhar']['name'] != ''){
            $uploadfile1 = $uploaddir . basename($_FILES['aadhar']['name']);
            if (move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadfile1)) {
                echo "File is valid, and was successfully uploaded.\n";
                $files_array[] = $_FILES['aadhar']['name'];
            } else {
                echo "Possible file upload attack!\n";
            }
            $newModel->setData('aadhar', $uploadurl . basename($_FILES['aadhar']['name']));
        }

        if($_FILES['pancard']['name'] != ''){
            $uploadfile2 = $uploaddir . basename($_FILES['pancard']['name']);
            if (move_uploaded_file($_FILES['pancard']['tmp_name'], $uploadfile2)) {
                echo "File is valid, and was successfully uploaded.\n";
                $files_array[] = $_FILES['pancard']['name'];
            } else {
                echo "Possible file upload attack!\n";
            }
            $newModel->setData('pancard', $uploadurl . basename($_FILES['pancard']['name']));
        }

        if($_FILES['postcheque']['name'] != ''){
            $uploadfile3 = $uploaddir . basename($_FILES['postcheque']['name']);
            if (move_uploaded_file($_FILES['postcheque']['tmp_name'], $uploadfile3)) {
                echo "File is valid, and was successfully uploaded.\n";
                $files_array[] = $_FILES['postcheque']['name'];
            } else {
                echo "Possible file upload attack!\n";
            }
            $newModel->setData('postcheque', $uploadurl . basename($_FILES['postcheque']['name']));
        }

        if($_FILES['bankst']['name'] != ''){
            $uploadfile4 = $uploaddir . basename($_FILES['bankst']['name']);
            if (move_uploaded_file($_FILES['bankst']['tmp_name'], $uploadfile4)) {
                echo "File is valid, and was successfully uploaded.\n";
                $files_array[] = $_FILES['bankst']['name'];
            } else {
                echo "Possible file upload attack!\n";
            }
            $newModel->setData('bankst', $uploadurl . basename($_FILES['bankst']['name']));
        }

        $newModel->save();
        Mage::getSingleton("core/session")->addSuccess('We have successfully saved your data');
    }
}