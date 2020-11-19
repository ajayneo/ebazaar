<?php
class Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
			
				array(
                    "label" => Mage::helper("eav")->__(""),
                    "value" =>  ""
                ),
                
                array(
                    "label" => Mage::helper("eav")->__("Abdul Kareem Pallan"),
                    "value" =>  1
                ),
	
                array(
                    "label" => Mage::helper("eav")->__("Abraham Varakala"),
                    "value" =>  2
                ),
	
                array(
                    "label" => Mage::helper("eav")->__("Amit Kumar Vishnoi"),
                    "value" =>  3
                ),
	
                array(
                    "label" => Mage::helper("eav")->__("Ashutosh Shukla"),
                    "value" =>  4
                ),
                array(
                    "label" => Mage::helper("eav")->__("Binesh Kumar Patasani"),
                    "value" =>  5
                ),
                
                array(
                    "label" => Mage::helper("eav")->__("Dhiraj Ahuja"),
                    "value" =>  6
                ),
    
                array(
                    "label" => Mage::helper("eav")->__("Mitul Sureshbhai Gandhi"),
                    "value" =>  7
                ),
    
                array(
                    "label" => Mage::helper("eav")->__("Mohammed Rizwan Uddin"),
                    "value" =>  8
                ),
    
                array(
                    "label" => Mage::helper("eav")->__("Prashant Vinayak Thorat"),
                    "value" =>  9
                ),

                array(
                    "label" => Mage::helper("eav")->__("Rajdeep Nandi"),
                    "value" =>  10
                ),
    
                array(
                    "label" => Mage::helper("eav")->__("Ratish Mehrotra"),
                    "value" =>  11
                ),
    
                /*array(
                    "label" => Mage::helper("eav")->__("Sanjeev Kumar Chand"),
                    "value" =>  12
                ),*/ 
    
                array(
                    "label" => Mage::helper("eav")->__("Taslim Arif"),
                    "value" =>  13
                ),

                array(
                    "label" => Mage::helper("eav")->__("Bedanta Haloi"),
                    "value" =>  14
                ),

                array(
                    "label" => Mage::helper("eav")->__("Ashish Vishwakarma"),
                    "value" =>  15
                ),

                array(
                    "label" => Mage::helper("eav")->__("Lokesh R"),
                    "value" =>  16
                ),
                 array(
                    "label" => Mage::helper("eav")->__("Birendra Kumar"),
                    "value" =>  17
                ),
                array(
                    "label" => Mage::helper("eav")->__("Kuntal Das"),
                    "value" =>  18
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Channel Play"),
                    "value" =>  19
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Bhaumin Patel"),
                    "value" =>  20
                ),
                array(
                    "label" => Mage::helper("eav")->__("Binod Kumar Singh"),
                    "value" =>  21
                ),
                array(
                    "label" => Mage::helper("eav")->__("Pratap Naidu"),
                    "value" =>  22
                ),
                array(
                    "label" => Mage::helper("eav")->__("Pradeep Munot"),
                    "value" =>  23
                ),
                array(
                    "label" => Mage::helper("eav")->__("Gagandeep Chhatwal"),
                    "value" =>  24
                ),
                array(
                    "label" => Mage::helper("eav")->__("Imran Khan"),
                    "value" =>  25
                ),
                array(
                    "label" => Mage::helper("eav")->__("Amarendra Kumar Das"),
                    "value" =>  26
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ugandhar Konda"),
                    "value" =>  27
                ),array(
                    "label" => Mage::helper("eav")->__("Kumar Paramtap"),
                    "value" =>  28
                ),
                array(
                    "label" => Mage::helper("eav")->__("Sourabh Soni"),
                    "value" =>  29
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ashish Patidar"),
                    "value" =>  30
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Amit Gujjar"),
                    "value" =>  31   
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Vishal Waghela"),
                    "value" =>  32
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Vivek Sisodiya"),   
                    "value" =>  33
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Manas Banerjee"),   
                    "value" =>  34
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Rishi Sharma"),   
                    "value" =>  35
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Biru Poria"),   
                    "value" =>  36
                ),
                array(
                    "label" => Mage::helper("eav")->__("Gaurav Bajpai"),    
                    "value" =>  37
                ),
                array(
                    "label" => Mage::helper("eav")->__("Rajesh Tiwari"),    
                    "value" =>  38
                ),
                array(
                    "label" => Mage::helper("eav")->__("Mithun Chaurasiya"),    
                    "value" =>  39
                ),
                array(
                    "label" => Mage::helper("eav")->__("Prasad Muduli"),    
                    "value" =>  40 
                ),
                array(
                    "label" => Mage::helper("eav")->__("Sumanta Singha"),    
                    "value" =>  41 
                ),
                array(
                    "label" => Mage::helper("eav")->__("Raj Kalet"),    
                    "value" =>  42 
                ),
                array(
                    "label" => Mage::helper("eav")->__("Jabir Hushan"),    
                    "value" =>  43  
                ),
                array(
                    "label" => Mage::helper("eav")->__("Gaurav Panchal"),    
                    "value" =>  44  
                ),
                array(
                    "label" => Mage::helper("eav")->__("Yogesh Yadav"),    
                    "value" =>  45  
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ramamoorthy Krishnan"),    
                    "value" =>  46
                ),
                array(
                    "label" => Mage::helper("eav")->__("PANKAJ KUMAR"),    
                    "value" =>  47
                ),
                array(
                    "label" => Mage::helper("eav")->__("Tanweer Ansari"),    
                    "value" =>  48
                ),
                array(
                    "label" => Mage::helper("eav")->__("Kapil Vyas"),    
                    "value" =>  49
                ),
                array(
                    "label" => Mage::helper("eav")->__("Vivekanand"),    
                    "value" =>  50
                ),
                array(
                    "label" => Mage::helper("eav")->__("Varinder Singh"),    
                    "value" =>  51
                ),    
                array(
                    "label" => Mage::helper("eav")->__("Sahil Mujoo"),    
                    "value" =>  52
                ),
                array(
                    "label" => Mage::helper("eav")->__("Chhitij"),    
                    "value" =>  53 
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Shailesh Singhal"),    
                    "value" =>  54 
                ), 
                array(
                    "label" => Mage::helper("eav")->__("Sandeep Mukherjee"),    
                    "value" =>  55
                ),      
            ); 
        }
        return $this->_options;  
    }   
     
    /** 
     * Retrieve option array
     * 
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option["value"]] = $option["label"];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option["value"] == $value) {
                return $option["label"];
            }
        }
        return false;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    /*public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            "type"      => "tinyint(1)",
            "unsigned"  => false,
            "is_null"   => true,
            "default"   => null,
            "extra"     => null
        );

        return $columns;
    }*/

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    /*public function getFlatIndexes()
    {
        $indexes = array();

        $index = "IDX_" . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            "type"      => "index",
            "fields"    => array($this->getAttribute()->getAttributeCode())
        );

        return $indexes;
    }*/

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    /*public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel("eav/entity_attribute")
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }*/

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getOptionArray2()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
            
                array(
                    "label" => Mage::helper("eav")->__(""),
                    "value" =>  ""
                ),
                
                array(
                    "label" => Mage::helper("eav")->__("Sandeep Mukherjee"),
                    "value" =>  1
                ),
          
            ); 
        }
        return $this->_options;  
    }   
}

			 