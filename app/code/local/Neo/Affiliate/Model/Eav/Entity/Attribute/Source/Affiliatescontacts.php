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
                // array(
                //     "label" => Mage::helper("eav")->__("Birendra Kumar"),
                //     "value" =>  17
                // ),
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
                // array(
                //     "label" => Mage::helper("eav")->__("Suraj Bhandari"),
                //     "value" =>  61
                // ), 
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
                // array(
                //     "label" => Mage::helper("eav")->__("Keval Chokshi"),
                //     "value" =>  126
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Syed Askari"),
                //     "value" =>  137
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Parth Purohit"),
                    "value" =>  138
                ),
                array(
                    "label" => Mage::helper("eav")->__("Taha Bawa"),
                    "value" =>  139
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Umang Khandelwal"),
                //     "value" =>  143
                // ),
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
                // array(
                //     "label" => Mage::helper("eav")->__("Hiraman"),
                //     "value" =>  150
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Rakesh Zaroo"),
                    "value" =>  151
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Laxmikant Jolad"),
                //     "value" =>  153
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("AAIMRA"),
                //     "value" =>  154
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Krunal"),
                //     "value" =>  155
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Sudhir"),
                //     "value" =>  156
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Ruchika"),
                //     "value" =>  157
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Romi"),
                //     "value" =>  158
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Vishnu"),
                //     "value" =>  159
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Chandan Paprunia"),
                    "value" =>  160
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("George"),
                //     "value" =>  161
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Samir Shah"),
                //     "value" =>  162
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Renuka Rajput"),
                //     "value" =>  163
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Chandpasha"),
                //     "value" =>  164
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Mayur Palande"),
                //     "value" =>  165
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Rohtas HariRam Kumavat"),
                //     "value" =>  166
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Mahesh Dhas"),
                //     "value" =>  167
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Ajay Singh"),
                //     "value" =>  168
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Royas Fonseca"),
                    "value" =>  169
                ),
                array(
                    "label" => Mage::helper("eav")->__("Kunal Deshmukh"),
                    "value" =>  170
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Pradeep Kumar"),
                //     "value" =>  171
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Rohit Pal Singh"),
                //     "value" =>  172
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("MANDEEP SINGH"),
                //     "value" =>  173
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Ankur Rana"),
                    "value" =>  174
                ),
                array(
                    "label" => Mage::helper("eav")->__("Jatin Kapoor"),
                    "value" =>  175
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Ashish Kumar"),
                //     "value" =>  176
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Rajesh Malik"),
                //     "value" =>  177
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Sandeep Kumar"),
                    "value" =>  178
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Vikas Ojha"),
                //     "value" =>  179
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Jay Krishna"),
                    "value" =>  180
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Pavan Reddy"),
                //     "value" =>  181
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Srinidhi Bagalkot"),
                //     "value" =>  182
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Senthamarai Kannan"),
                //     "value" =>  183
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Muhammed Niyamathullah"),
                    "value" =>  184
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ankur Katiyar"),
                    "value" =>  185
                ),
                array(
                    "label" => Mage::helper("eav")->__("Jarnail Singh"),
                    "value" =>  186
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Zameer Ahamad"),
                //     "value" =>  187
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Shamsher Ali Sheikh"),
                //     "value" =>  188
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Debashish Mondal"),
                //     "value" =>  189
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Abhishek Raj"),
                    "value" =>  190
                ),
                array(
                    "label" => Mage::helper("eav")->__("Nagesh Gajji"),
                    "value" =>  191
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Abhinandan Kumar"),
                //     "value" =>  192
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Manjula MP"),
                //     "value" =>  193
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Mohammad Naim"),
                    "value" =>  194
                ),
                array(
                    "label" => Mage::helper("eav")->__("Mohammad Imran"),
                    "value" =>  195
                ),
                array(
                    "label" => Mage::helper("eav")->__("Aishaparveen Shaikh"),
                    "value" =>  196
                ),
                array(
                    "label" => Mage::helper("eav")->__("Pooja Chauhan"),
                    "value" =>  197
                ),
                array(
                    "label" => Mage::helper("eav")->__("Zeenat Shaikh"),
                    "value" =>  198
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ilyaz Ahmed"),
                    "value" =>  199
                ),
                array(
                    "label" => Mage::helper("eav")->__("Abdul Athiq"),
                    "value" =>  200
                ),
                array(
                    "label" => Mage::helper("eav")->__("Nisha Mishra"),
                    "value" =>  201
                ),
                array(
                    "label" => Mage::helper("eav")->__("Mohsin Basha T K"),
                    "value" =>  202
                ),
                array(
                    "label" => Mage::helper("eav")->__("Chethan A S"),
                    "value" =>  203
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Abdul Rahim"),
                //     "value" =>  204
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Suraj M S"),
                    "value" =>  205
                ),
                array(
                    "label" => Mage::helper("eav")->__("Vamshi V"),
                    "value" =>  206
                ),
                array(
                    "label" => Mage::helper("eav")->__("Moinuddin"),
                    "value" =>  207
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Abdul Razak"),
                //     "value" =>  208
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Shalini K"),
                //     "value" =>  209
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Murthuza Ali BM"),
                    "value" =>  210
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Shaikh Abdulla"),
                //     "value" =>  212
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Lokesh G"),
                //     "value" =>  213
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Lubna Javed"),
                //     "value" =>  214
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Rajesh Soni"),
                //     "value" =>  215
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Venkateshan C"),
                //     "value" =>  216
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Yogin Rupera"),
                    "value" =>  217
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Sukhendu Bera"),
                //     "value" =>  218
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Shriniwas Karekar"),
                    "value" =>  219
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Jayraj Parashetti"),
                //     "value" =>  220
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Mahadeva M"),
                    "value" =>  222
                ),
                array(
                    "label" => Mage::helper("eav")->__("Mohammed Zameer Pasha"),
                    "value" =>  223
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Chandresh Jadon"),
                //     "value" =>  226
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Santosh Nadar"),
                //     "value" =>  228
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Vidhi Gohel"),
                    "value" =>  229
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Biswajit"),
                //     "value" =>  231
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Amit Harrison"),
                    "value" =>  232
                ),
                array(
                    "label" => Mage::helper("eav")->__("Rekibuddin Ahmed"),
                    "value" =>  233
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Murtuza"),
                //     "value" =>  234
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Vidhhi"),
                //     "value" =>  235
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Prasad Kokadwar"),
                //     "value" =>  236
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Jayesh Bagul"),
                //     "value" =>  237
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Omkar Gumaste"),
                //     "value" =>  238
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Sagar Shinde"),
                //     "value" =>  239
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Ankit"),
                    "value" =>  240
                )					
            ); 
        }

        $this->_options = array();
        $asmCollection = Mage::getModel('asmdetail/asm')->getCollection();
        // $asmCollection->addFieldToFilter("enabled",1);
        $this->_options[] = array(   
                            "label" => "", 
                            "value" => ""
                        );
        foreach ($asmCollection as $asm) {
            $this->_options[] = array(   
                            "label" => Mage::helper("eav")->__($asm->getName()), 
                            "value" =>   $asm->getId()
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
                // array(
                //     "label" => Mage::helper("eav")->__("Praveen Shrivastva"),
                //     "value" =>  3
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Sathish Vittal"),
                    "value" =>  4
                ),
                // array(
                //     "label" => Mage::helper("eav")->__("Somsubhra Bagchi"),
                //     "value" =>  7
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Nabajyoti Bora"),
                //     "value" =>  9
                // ),
                // array(
                //     "label" => Mage::helper("eav")->__("Keval Chokshi"),
                //     "value" =>  8
                // ),
                array(
                    "label" => Mage::helper("eav")->__("Info"),
                    "value" =>  10
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ankit Goyal"),
                    "value" =>  11
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ankur Dubey"),
                    "value" =>  12
                ),
                array(
                    "label" => Mage::helper("eav")->__("Rajib Chowdhary"),
                    "value" =>  13
                ),
                array(
                    "label" => Mage::helper("eav")->__("Ravi Vattikonda"),
                    "value" =>  14
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Syed Shakir"),
                    "value" =>  15
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Ajay Yadav"),
                    "value" =>  16
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Birendra Kumar"),
                    "value" =>  17
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Keval Choksi"),
                    "value" =>  18
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Kushlesh Sadhu"),
                    "value" =>  19
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Prakash Purohit"),
                    "value" =>  20
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Rajesh Mishra"),
                    "value" =>  21
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Rajesh Soni"),
                    "value" =>  22
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Rakesh Zaroo"),
                    "value" =>  23
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Susheel Kamble"),
                    "value" =>  24
                ),                                                               
                array(
                    "label" => Mage::helper("eav")->__("Taha Bawa"),
                    "value" =>  25
                )                
            ); 
        }

        $this->_options = array();
        $rsmCollection = Mage::getModel('asmdetail/rsm')->getCollection();
        foreach ($rsmCollection as $rsm) {
            $this->_options[] = array(   
                            "label" => Mage::helper("eav")->__($rsm->getName()), 
                            "value" =>   $rsm->getId()
                        );
        }
        asort($this->_options);
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

			 
