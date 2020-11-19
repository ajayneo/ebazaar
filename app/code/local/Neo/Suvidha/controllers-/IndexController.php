<?php
class Neo_Suvidha_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      if(Mage::getSingleton('customer/session')->isLoggedIn()) {
          $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Credit Suvidha Program"));
              $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
            $breadcrumbs->addCrumb("home", array(
                    "label" => $this->__("Home Page"),
                    "title" => $this->__("Home Page"),
                    "link"  => Mage::getBaseUrl()
                ));

            $breadcrumbs->addCrumb("credit suvidha program", array(
                    "label" => $this->__("Credit Suvidha Program"),
                    "title" => $this->__("Credit Suvidha Program")
                ));

        $this->renderLayout(); 
      }else{
          Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login?form=shubh-labh'));
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
            $cusomer = array();    
            $cusomer['name'] = $post_data['customer_name'];
            $cusomer['mobile'] = $post_data['mobile'];
            $cusomer['email'] = $post_data['email_id'];
            $modelS = Mage::getModel('suvidha/creditsuvidha')->load($entity_id);
                $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha'.DS;
                if(!is_dir($uploaddir.$entity_id)){
                    mkdir($uploaddir.$entity_id, 0777, true);
                }
                $uploaddir = $uploaddir . DS . $entity_id .DS;
                // echo "string".$uploaddir;exit;

                if($_FILES['aadhar']['name'] != ''){
                    $uploadfile1 = $uploaddir . basename($_FILES['aadhar']['name']);
                    if (move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadfile1)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['aadhar']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('aadhar', $uploadfile1);
                }

                if($_FILES['pancard']['name'] != ''){
                    $uploadfile2 = $uploaddir . basename($_FILES['pancard']['name']);
                    if (move_uploaded_file($_FILES['pancard']['tmp_name'], $uploadfile2)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['pancard']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('pancard',$uploadfile2);
                }

                if($_FILES['postcheque']['name'] != ''){
                    $uploadfile3 = $uploaddir . basename($_FILES['postcheque']['name']);
                    if (move_uploaded_file($_FILES['postcheque']['tmp_name'], $uploadfile3)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['postcheque']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('postcheque',$uploadfile3);
                }

                if($_FILES['sign_file1']['name'] != ''){
                    $uploadfile5 = $uploaddir . basename($_FILES['sign_file1']['name']);
                    if (move_uploaded_file($_FILES['sign_file1']['tmp_name'], $uploadfile5)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['sign_file1']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('sign_file1',$uploadfile5);
                }

                if($_FILES['sign_file2']['name'] != ''){
                    $uploadfile6 = $uploaddir . basename($_FILES['sign_file2']['name']);
                    if (move_uploaded_file($_FILES['sign_file2']['tmp_name'], $uploadfile6)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['sign_file2']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('sign_file2',$uploadfile6);
                }

                if($_FILES['sign_file3']['name'] != ''){
                    $uploadfile7 = $uploaddir . basename($_FILES['sign_file3']['name']);
                    if (move_uploaded_file($_FILES['sign_file3']['tmp_name'], $uploadfile7)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['sign_file3']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('sign_file3',$uploadfile7);
                }

                if($_FILES['bankst']['name'] != ''){
                    $uploadfile4 = $uploaddir . basename($_FILES['bankst']['name']);
                    if (move_uploaded_file($_FILES['bankst']['tmp_name'], $uploadfile4)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['bankst']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $modelS->setData('bankst',$uploadfile4);
                }

            $modelS->addData($this->getRequest()->getPost());
            $modelS->save();
            // $this            
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
                $newModel->setData($post_data)->save();

                // print_r($newModel->getData('id'));exit;
                $entity_id1 = $newModel->getData('id');

                $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha'.DS;
                if(!is_dir($uploaddir.$entity_id1 )){
                    mkdir($uploaddir.$entity_id1 , 0777, true);
                }
                $uploaddir = $uploaddir . DS . $entity_id1 .DS;

                // $uploaddir = Mage::getBaseDir().DS.'kaw17842knlpREGdasp9045'. DS . 'suvidha' . DS .'creditsuvidha'.DS;

                if($_FILES['aadhar']['name'] != ''){
                    $uploadfile1 = $uploaddir . basename($_FILES['aadhar']['name']);
                    if (move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadfile1)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['aadhar']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('aadhar', $uploadfile1);
                }

                if($_FILES['pancard']['name'] != ''){
                    $uploadfile2 = $uploaddir . basename($_FILES['pancard']['name']);
                    if (move_uploaded_file($_FILES['pancard']['tmp_name'], $uploadfile2)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['pancard']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('pancard',$uploadfile2);
                }

                if($_FILES['postcheque']['name'] != ''){
                    $uploadfile3 = $uploaddir . basename($_FILES['postcheque']['name']);
                    if (move_uploaded_file($_FILES['postcheque']['tmp_name'], $uploadfile3)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['postcheque']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('postcheque',$uploadfile3);
                }

                if($_FILES['sign_file1']['name'] != ''){
                    $uploadfile5 = $uploaddir . basename($_FILES['sign_file1']['name']);
                    if (move_uploaded_file($_FILES['sign_file1']['tmp_name'], $uploadfile5)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['sign_file1']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('sign_file1',$uploadfile5);
                }

                if($_FILES['sign_file2']['name'] != ''){
                    $uploadfile6 = $uploaddir . basename($_FILES['sign_file2']['name']);
                    if (move_uploaded_file($_FILES['sign_file2']['tmp_name'], $uploadfile6)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['sign_file2']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('sign_file2',$uploadfile6);
                }

                if($_FILES['sign_file3']['name'] != ''){
                    $uploadfile7 = $uploaddir . basename($_FILES['sign_file3']['name']);
                    if (move_uploaded_file($_FILES['sign_file3']['tmp_name'], $uploadfile7)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['sign_file3']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('sign_file3',$uploadfile7);
                }

                if($_FILES['bankst']['name'] != ''){
                    $uploadfile4 = $uploaddir . basename($_FILES['bankst']['name']);
                    if (move_uploaded_file($_FILES['bankst']['tmp_name'], $uploadfile4)) {
                        echo "File is valid, and was successfully uploaded.\n";
                        $files_array[] = $_FILES['bankst']['name'];
                    } else {
                        echo "Possible file upload attack!\n";
                    }
                    $newModel->setData('bankst',$uploadfile4);
                }

            $newModel->save();

            Mage::helper('suvidha')->sendRequestEmail($files_array, $customer, $entity_id1, $post_data['asm_map']);
            Mage::getSingleton("core/session")->addSuccess('Thank you for submitting your request. We will get back to you shortly.');
          }
          catch(Exception $e){
            Mage::getSingleton("core/session")->addError($e->getMessage());
          }
        }
        $this->_redirectUrl(Mage::getBaseUrl().'suvidha/index');
    }
}