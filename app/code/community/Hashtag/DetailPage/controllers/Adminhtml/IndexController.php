<?php
/**
 *
 * @category   Hashtag
 * @package    Hashtag_Detailpage
 * @author     Bhavesh Gadoya <bhavesh.gadoya89@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
class Hashtag_DetailPage_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    /**
     *
      */
    public function indexAction() {
        $customerId = null;
        $storeId = 1;
        $createtag_model = Mage::getModel("detailpage/createtag");
        $optionName = $this->getRequest()->getPost("key");
        $optionValue = $this->getRequest()->getPost("value");
        $code = $this->getRequest()->getPost("attribute");
        $category_id = $this->getRequest()->getPost("category_id");
//        $category = Mage::getModel('catalog/category')->load(24);
//        $model = Mage::getModel("catalog/product")->getCollection()->addAttributeToFilter("price",array("neq"=>0))->addAttributeToFilter("touch_screen",array("eq"=>"Yes"))->addAttributeToFilter("status",array("eq"=>1))->addCategoryFilter($category)->load();
//        $data = $model->getData();
//        for($p = 0 ; $p < count($data) ; $p++)
//        {
//            $product_ids[$p]["id"] = $data[$p]["entity_id"];
//            $product_ids[$p]["price"] = $data[$p]["price"]; 
//        }

//     if(preg_match("/\bandroid\b/i", $optionName))
//    {
//               //$optionName = preg_match("");
//           }
//          else
//          {
//             echo $optionName." is not android";
//             exit;
//          }
    
//        $createtag_model->setTagName($optionName);
//        $tagModel = Mage::getModel("tag/tag");
//        $createtag_model->setTagModel($tagModel);
//        $createtag_model->filterTag();
//       
       // $createtag_model->createTag($tagModel);
        $product_ids = $createtag_model->filterProduct($attribute_code,$optionValue,$category_id,"select");
//        for($p = 0; $p < count($product_ids); $p++) {
//            $relationStatus = $tagModel->saveRelation($product_ids[$p]["id"], null, 1);
//            $counter[$relationStatus][] = $optionName;
//            $this->_fillMessageBox($counter);
//        }
 //       $optionName = "Basic phones under Rs ";
        for($m=0;$m<count($product_ids);$m++)
        {
            $price = (int) $product_ids[$m]["price"];
            if ($price == "" || $price == 0)
            continue;
            $len = strlen($price) - 1;
            $div = 1;
            for ($c = 0; $c < $len; $c++) {
                $div = $div * 10;
            }
            if ($price % $div == 0) {
                $final_output = $price;
            } else {
                $final_output = ceil($price / $div) * $div;
            }
             if(strlen($price)==5)
            {
                $multiplier = ceil($price/5000);
                $reminder = $price % 5000;
                if($reminder!=0)
                 {
                        $final_output=5000 * $multiplier;
                 }
                else {
                    $final_output = $price;
                }
            }
           
            $category_name = $optionName;
            $newTagname1 = $category_name . $final_output;
            //$success = $this->priceTag($newTagname1, $final_output, 4);
            $createtag_model1 = Mage::getModel("detailpage/createtag");
            $createtag_model1->setTagName($newTagname1);
            $tagModel1 = Mage::getModel("tag/tag");
            $createtag_model1->setTagModel($tagModel1);
            $createtag_model1->filterTag();
            $products = $createtag_model1->filterprice($final_output, 24);
           
            for($k = 0; $k < count($products); $k++) {
            if(is_array($products[$k]))
            {
             for($b=0;$b<count($products[$k]);$b++)
            {
              $relationStatus1 = $tagModel1->saveRelation($products[$b][$k], null, 1);
              $counter[$relationStatus1][] = $newTagname1;
              $this->_fillMessageBox($counter);
            }
            }
            else{
            $relationStatus1 = $tagModel1->saveRelation($products[$k], null, 1);
            $counter[$relationStatus1][] = $newTagname1;
            $this->_fillMessageBox($counter);
            }
            }
        }
         echo "A tag named " . "Dual sim" . " is generated or associate with products ";
         exit;
   }

    public function priceTag($newTagname1, $final_output, $category_id) {
        $createtag_model1 = Mage::getModel("detailpage/createtag");
        $createtag_model1->setTagName($newTagname1);
        $tagModel1 = Mage::getModel("tag/tag");
        $createtag_model1->setTagModel($tagModel1);
        $createtag_model1->filterTag();
        $products = $createtag_model1->filterprice($final_output, $category_id);
        for($k = 0; $k < count($products); $k++) {
            $relationStatus1 = $tagModel1->saveRelation($products[$k], null, 1);
            $counter[$relationStatus1][] = $newTagname1;
            $this->_fillMessageBox($counter);
        }
        return 1;
     }

//    public function saveAction() {
//        $customerSession = Mage::getSingleton('customer/session');
////        if(!$customerSession->authenticate($this)) {
////            return;
////        }
////       // $tagName    = (string) $this->getRequest()->getQuery('productTagName');
//        $attribute_code = $this->getRequest()->getPost("attributes");
//
//
//        $tagName = "myProduct";
//        $productId = "3236";
//        //  $productId  = (int)$this->getRequest()->getParam('product');
//
//        if (strlen($tagName) && $productId) {
//            $session = Mage::getSingleton('catalog/session');
//            $product = Mage::getModel('catalog/product')
//                    ->load($productId);
//            if (!$product->getId()) {
//                $session->addError($this->__('Unable to save tag(s).'));
//            } else {
//                try {
//                    $customerId = $customerSession->getCustomerId();
//                    $storeId = 1;
//
//                    $tagModel = Mage::getModel('tag/tag');
//
//                    // added tag relation statuses
//                    $counter = array(
//                        Mage_Tag_Model_Tag::ADD_STATUS_NEW => array(),
//                        Mage_Tag_Model_Tag::ADD_STATUS_EXIST => array(),
//                        Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS => array(),
//                        Mage_Tag_Model_Tag::ADD_STATUS_REJECTED => array()
//                    );
//
//                    $tagNamesArr = $this->_cleanTags($this->_extractTags($tagName));
//                    foreach ($tagNamesArr as $tagName) {
//                        // unset previously added tag data
//                        $tagModel->unsetData()
//                                ->loadByName($tagName);
//
//                        if (!$tagModel->getId()) {
//                            $tagModel->setName($tagName)
//                                    ->setFirstCustomerId($customerId)
//                                    ->setFirstStoreId($storeId)
//                                    ->setStatus($tagModel->getApprovedStatus())
//                                    ->save();
//                        }
//                        $relationStatus = $tagModel->saveRelation($productId, $customerId, $storeId);
//                        $counter[$relationStatus][] = $tagName;
//                    }
//                    $this->_fillMessageBox($counter);
//                } catch (Exception $e) {
//                    Mage::logException($e);
//                    $session->addError($this->__('Unable to save tag(s).'));
//                }
//            }
//        }
//        // $this->_redirectReferer();
//        return "success";
//    }

    /**
     * Checks inputed tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    protected function _extractTags($tagNamesInString) {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clears the tag from the separating characters.
     *
     * @param array $tagNamesArr
     * @return array
     */
    protected function _cleanTags(array $tagNamesArr) {
        foreach ($tagNamesArr as $key => $tagName) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if ($tagNamesArr[$key] == '') {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }

    public function getvaluesAction() {
        $attribute_code = $this->getRequest()->getPost("attributes");
        //foreach($attribute_code as $key => $code) {
        $code = $attribute_code;
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($code)->getFirstItem();
        
        $attributeId = $attributeInfo->getAttributeId();
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        if($attribute->getData("frontend_input")=="text")
        {
            $data[0]["label"] = "Tablets ";
            $data[0]["value"] = "Tablets ";
        }
        else
        {
         $attributeOptions = $attribute->getSource()->getAllOptions(false);
         for ($i = 0; $i < count($attributeOptions); $i++) {
            $data[$i]["label"] = $attributeOptions[$i]['label'];
            $data[$i]["value"] = $attributeOptions[$i]['value'];
        }   
        }
       
        echo json_encode($data);
        exit;
    }

    /**
     * Fill Message Box by success and notice messages about results of user actions.
     *
     * @param array $counter
     * @return void
     */
    protected function _fillMessageBox($counter) {
        $session = Mage::getSingleton('catalog/session');
        $helper = Mage::helper('core');

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_NEW])) {
            $session->addSuccess(
                    $this->__('%s tag(s) have been accepted for moderation.', count($counter[Mage_Tag_Model_Tag::ADD_STATUS_NEW]))
            );
        }

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_EXIST])) {
            foreach ($counter[Mage_Tag_Model_Tag::ADD_STATUS_EXIST] as $tagName) {
                $session->addNotice(
                        $this->__('Tag "%s" has already been added to the product.', $helper->escapeHtml($tagName))
                );
            }
        }

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS])) {
            foreach ($counter[Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS] as $tagName) {
                $session->addSuccess(
                        $this->__('Tag "%s" has been added to the product.', $helper->escapeHtml($tagName))
                );
            }
        }

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_REJECTED])) {
            foreach ($counter[Mage_Tag_Model_Tag::ADD_STATUS_REJECTED] as $tagName) {
                $session->addNotice(
                        $this->__('Tag "%s" has been rejected by administrator.', $helper->escapeHtml($tagName))
                );
            }
        }
    }

    /**
     * @return Ebizmarts_AbandonedCart_Adminhtml_AbandonedorderController
     */
//    protected function _initAction()
//    {
//        $this->loadLayout()
//            // Make the active menu match the menu config nodes (without 'children' inbetween)
//            ->_setActiveMenu('newsletter/ebizmarts_emails')
//            ->_title($this->__('Newsletter'))->_title($this->__('Emails Sent'))
//            ->_addBreadcrumb($this->__('Newsletter'), $this->__('Newsletter'))
//            ->_addBreadcrumb($this->__('abandonedorder'), $this->__('Mails'));
//
//        return $this;
//    }
//
//    /**
//     *
//     */
//    public function exportCsvAction()
//    {
//        $fileName   = 'orders.csv';
//        $grid       = $this->getLayout()->createBlock('ebizmarts_abandonedcart/adminhtml_abandonedmails_grid');
//        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
//    }
//
//    /**
//     *  Export order grid to Excel XML format
//     */
//    public function exportExcelAction()
//    {
//        $fileName   = 'orders.xml';
//        $grid       = $this->getLayout()->createBlock('ebizmarts_abandonedcart/adminhtml_abandonedmails_grid');
//        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
//    }
//
//    /**
//     *
//     */
//    public function gridAction()
//    {
//        $this->loadLayout(false);
//        $this->renderLayout();
//    }
//
//    /**
//     *
//     */
}
