<?php   
class Neo_Gadget_Block_Index extends Mage_Core_Block_Template{   


	public function getProductCollection($brand)
	{
		$productCollection = Mage::getModel("gadget/gadget")->getCollection()->addFieldToFilter('brand',$brand)->getData();
		return $productCollection;
	}


	public function getProduct($id)
	{
		$product = Mage::getModel("gadget/gadget")->load($id)->getData();
		return $product;
	}

	public function getcategories(){
		
		try {
			$bannerslider_id = 4;


			$banner = Mage::getModel('bannerslider/banner')->getCollection();

			if($banner){
				$banner->addFieldToFilter('bannerslider_id', $bannerslider_id);

				$banners = $banner->getData();

				$i = 0;
				foreach ($banners as $key => $value) {
					$return[$i]['name'] = $value['name'];
					$return[$i]['image_alt'] = $value['image_alt'];
					$return[$i]['image'] = $this->getBannerImage($value['image']);

					$i++;
				}
			}

			return $return;			
		} catch (Exception $e) {
			return $return;
		}	
	}

	public function reImageName($imageName) {

        $subname = substr($imageName, 0, 2);
        $array = array();
        $subDir1 = substr($subname, 0, 1);
        $subDir2 = substr($subname, 1, 1);
        $array[0] = $subDir1;
        $array[1] = $subDir2;
        $name = $array[0] . '/' . $array[1] . '/' . $imageName;

        return strtolower($name);
    }

    protected function getBannerImage($image) {  
        $name = $this->reImageName($image);
        return Mage::getBaseUrl('media') . 'bannerslider' . '/' . $name;
    }

    public function getGadgetBrandImages()
    {
    	$attributeId = 763;
 
		$attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')   
                ->setPositionOrder('asc')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter()
                ->load();

        return $attributeOptionSingle->getData();
    }

    public function getGadgetBrandImagesLaptop() 
    {
    	$attributeId = 777;
 
		$attributeOptionSingle = Mage::getResourceModel('eav/entity_attribute_option_collection')   
                ->setPositionOrder('asc')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter()
                ->load();

        return $attributeOptionSingle->getData();
    }

    protected function getGadgetBaseUrl(){
    	return $this->getBaseUrl().'gadget/';
    }

}