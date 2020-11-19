<?php
class Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array ASM
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
				array(
                    "label" => Mage::helper("eav")->__(""),
                    "value" =>  ""
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Abraham Varakala"),
                //     "value" =>  2
                // ),
                array(   
                    "label" => Mage::helper("eav")->__("Abu Irshad"), 
                    "value" =>  90
                ),
	           array(
                    "label" => Mage::helper("eav")->__("Ashutosh Shukla"),
                    "value" =>  4
                ),
                array(
                    "label" => Mage::helper("eav")->__("Birendra Kumar"),
                    "value" =>  17
                ),
                array(
                    "label" => Mage::helper("eav")->__("Binod Kumar Singh"),
                    "value" =>  21
                ),
                array(
                    "label" => Mage::helper("eav")->__("Mohammed Rizwan Uddin"),
                    "value" =>  8
                ),
                // array(  
                //     "label" => Mage::helper("eav")->__("Nikhil Velloth"), 
                //     "value" =>  88
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Pradeep Munot"),
                    "value" =>  23
                ),  
                array(
                    "label" => Mage::helper("eav")->__("Raj Kalet"),    
                    "value" =>  42 
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ramamoorthy Krishnan"),    
                    "value" =>  46
                ),
                array(  
                    "label" => Mage::helper("eav")->__("Ravi Amrit"), 
                    "value" =>  82
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Sahil Mujoo"),    
                //     "value" =>  52
                // ),
                // array(  
                //     "label" => Mage::helper("eav")->__("Sandeep Sawarkar"), 
                //     "value" =>  97 
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Shailesh Singhal"),    
                    "value" =>  54 
                ),  
                // array(
                //     "label" => Mage::helper("eav")->__("Sohan Karunamoorthy"),
                //     "value" =>  58
                // ), 
                // array(  
                //     "label" => Mage::helper("eav")->__("Subham Dixit"), 
                //     "value" =>  69
                // ),  
                array(
                    "label" => Mage::helper("eav")->__("Suraj Bhandari"),
                    "value" =>  61
                ), 
                // array(  
                //     "label" => Mage::helper("eav")->__("Vijay Kumar Guvvala"), 
                //     "value" =>  71
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Vivek Sisodiya"),   
                    "value" =>  33
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Raman"),
                //     "value" =>  106
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Sagar"),
                //     "value" =>  107
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Satnam"),
                //     "value" =>  108
                // ),
                array(
                    "label" => Mage::helper("eav")->__("KEVAL CHOKSHI"),
                    "value" =>  126
                ),
                array(
                    "label" => Mage::helper("eav")->__("Syed Hussain"),
                    "value" =>  137
                ),
                array(
                    "label" => Mage::helper("eav")->__("Parth Purohit"),
                    "value" =>  138
                ),
                array(
                    "label" => Mage::helper("eav")->__("Tahaa Bawa"),
                    "value" =>  139
                ),
                array(
                    "label" => Mage::helper("eav")->__("Umang Khandelwal"),
                    "value" =>  143
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Vidit"),
                //     "value" =>  144
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Rahul Kaushik"),
                //     "value" =>  145
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Vishwajeet Kumar"),
                //     "value" =>  146
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Pushpendra"),
                //     "value" =>  147
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Abhitosh Sinha"),
                //     "value" =>  148
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Ashish Patidar"),
                //     "value" =>  149
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Hiraman"),
                    "value" =>  150
                ),
                array(
                    "label" => Mage::helper("eav")->__("Rakesh Zaroo"),
                    "value" =>  151
                ),
                array(
                    "label" => Mage::helper("eav")->__("Laxmikant Jolad"),
                    "value" =>  153
                ),
                array(
                    "label" => Mage::helper("eav")->__("AAIMRA"),
                    "value" =>  154
                ),
                array(
                    "label" => Mage::helper("eav")->__("Krunal"),
                    "value" =>  155
                ),
                array(
                    "label" => Mage::helper("eav")->__("Sudhir"),
                    "value" =>  156
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Ruchika"),
                //     "value" =>  157
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Romi"),
                //     "value" =>  158
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Vishnu"),
                    "value" =>  159
                ),
                array(
                    "label" => Mage::helper("eav")->__("Chandan"),
                    "value" =>  160
                ),
                array(
                    "label" => Mage::helper("eav")->__("George"),
                    "value" =>  161
                ),
                array(
                    "label" => Mage::helper("eav")->__("Samir Shah"),
                    "value" =>  162
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Renuka Rajput"),
                //     "value" =>  163
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Chandpasha"),
                //     "value" =>  164
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Mayur Palande"),
                    "value" =>  165
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Rohtas HariRam Kumavat"),
                //     "value" =>  166
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Mahesh Dhas"),
                //     "value" =>  167
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Ajay"),
                    "value" =>  168
                ),
                array(
                    "label" => Mage::helper("eav")->__("Royas"),
                    "value" =>  169
                ),
                array(
                    "label" => Mage::helper("eav")->__("Kunal"),
                    "value" =>  170
                ),
                array(
                    "label" => Mage::helper("eav")->__("Pradeep Kumar"),
                    "value" =>  171
                ),
                array(
                    "label" => Mage::helper("eav")->__("Rohit Pal Singh"),
                    "value" =>  172
                ),
                array(
                    "label" => Mage::helper("eav")->__("MANDEEP SINGH"),
                    "value" =>  173
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ankur Rana"),
                    "value" =>  174
                ),
                array(
                    "label" => Mage::helper("eav")->__("Jatin Kapoor"),
                    "value" =>  175
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ashish Kumar"),
                    "value" =>  176
                ),
                array(
                    "label" => Mage::helper("eav")->__("Rajesh Malik"),
                    "value" =>  177
                ),
                array(
                    "label" => Mage::helper("eav")->__("Sandeep Kumar"),
                    "value" =>  178
                ),
                array(
                    "label" => Mage::helper("eav")->__("Vikas Ojha"),
                    "value" =>  179
                ),
                array(
                    "label" => Mage::helper("eav")->__("Jaya Krishna"),
                    "value" =>  180
                ),
                array(
                    "label" => Mage::helper("eav")->__("Pavan Reddy"),
                    "value" =>  181
                ),
                array(
                    "label" => Mage::helper("eav")->__("Srinidhi Bagalkot"),
                    "value" =>  182
                ),
                array(
                    "label" => Mage::helper("eav")->__("Senthamarai Kannan"),
                    "value" =>  183
                ),
                array(
                    "label" => Mage::helper("eav")->__("Muhammed Niyamathullah"),
                    "value" =>  184
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ankur Katiyar"),
                    "value" =>  185
                )
            ); 
        }
        asort($this->_options);
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
     * @return array RSM
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
                    "label" => Mage::helper("eav")->__("Praveen Shrivastva"),
                    "value" =>  3
                ),
                array(
                    "label" => Mage::helper("eav")->__("Sathish Vittal"),
                    "value" =>  4
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Somsubhra Bagchi"),
                //     "value" =>  7
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Nabajyoti Bora"),
                    "value" =>  9
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Keval Chokshi"),
                //     "value" =>  8
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Info"),
                    "value" =>  10
                )

            ); 
        }
        return $this->_options;  
    }   

    static public function getOptionText2($value)
    {
        $obj = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts;
        $options = $obj->getOptionArray2();
        foreach ($options as $option) {
            if ($option["value"] == $value) {
                return $option["label"];
            }
        }
        return false;
    }

    /**
     * Retrieve all options array
     *
     * @return array TSM
     */
    public function getOptionArray3()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
            
                array(
                    "label" => Mage::helper("eav")->__(""),
                    "value" =>  ""
                ),
                array(
                    "label" => Mage::helper("eav")->__("ABHPAN001"),
                    "value" =>  98
                ),
                array(
                    "label" => Mage::helper("eav")->__("AMISHA002"),
                    "value" =>  99
                ),
                array(
                    "label" => Mage::helper("eav")->__("ATUSHU003"),
                    "value" =>  100
                ),
                array(
                    "label" => Mage::helper("eav")->__("JAYVAI004"),
                    "value" =>  101
                ),
                array(
                    "label" => Mage::helper("eav")->__("KUNDAS005"),
                    "value" =>  102
                ),
                array(
                    "label" => Mage::helper("eav")->__("MAHPAR006"),
                    "value" =>  103
                ),
                array(
                    "label" => Mage::helper("eav")->__("NITBJA007"),
                    "value" =>  104
                ),
                array(
                    "label" => Mage::helper("eav")->__("PAVPAN008"),
                    "value" =>  105
                )
            ); 
        }
        return $this->_options;  
    }

    public function getRsmbyasm($asm_map){
        $asmdetail = Mage::getModel('asmdetail/asmdetail')->getCollection();
        // $asmdetail->addFieldToFilter('asm')
        $data_array = array();
        $rsm_name = '';
        foreach ($asmdetail as $asm){
            # code...
            if($asm_map == $asm->getName()){
                $rsm_name = $asm->getRsmname();
                $rsm_name = $this->getOptionText2($rsm_name);
            } 
        }
        
        return $rsm_name;
    }
}

			 
