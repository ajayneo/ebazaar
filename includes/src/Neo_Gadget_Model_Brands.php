<?php
class Neo_Gadget_Model_Brands extends Mage_Core_Model_Abstract
{

    public static function getBrands()
    {
    	return 
    		array(
	    			' '             =>  'Please Select Brand',
                    'apple'         =>  'Apple',
                    'asus'          =>  'Asus',
                    'blackberry'    =>  'Black Berry',
                    'gionee'        =>  'Gionee',
                    'google'        =>  'Google',
                    'htc'           =>  'HTC',
                    'huawei'        =>  'Huawei',
                    'intex'         =>  'Intex',
                    'karbonn'       =>  'Karbonn',
                    'lava'          =>  'Lava',
                    'lenovo'        =>  'Lenovo',
                    'lg'            =>  'LG',
                    'meizu'         =>  'Meizu',
                    'micromax'      =>  'Micromax',
                    'motorola'      =>  'Motorola',
                    'nokia'         =>  'Nokia',
                    'oneplus'       =>  'One Plus',
                    'oppo'          =>  'Oppo',
                    'panasonic'     =>  'Panasonic',
                    'samsung'       =>  'Samsung',
                    'sony'          =>  'Sony',
                    'xiaomi'        =>  'Xiaomi',
                    'xolo'          =>  'Xolo',
                    'alcatel'       =>  'Alcatel',
                    'honor'         =>  'Honor',
                    'microsoft'     =>  'Microsoft',
                    'yota'          =>  'Yota',
    			);
    }

    public static function getBrandsGrid()
    {
    	$brands = array();

    	$brands =  Neo_Gadget_Model_Brands::getBrands();

    	unset($brands[' ']);
    	return $brands;
    }

}
	 