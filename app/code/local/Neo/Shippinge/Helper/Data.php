<?php
	class Neo_Shippinge_Helper_Data extends Mage_Core_Helper_Abstract
	{
		public function getWarehouseAddress($orderId = NULL, $warehouse = NULL)
		{
			$stockLocationModelRaw = Mage::getModel('stocklocation/location');
            $stockLocationModel = $stockLocationModelRaw->getCollection()->addFieldToSelect(array('stock_location', 'id'))->addFieldToFilter('order_id', $orderId)->getFirstItem()->getData();

            //IMP - not allowed

            $warehouseAddress = array();
            if($warehouse != NULL){
                $stockLocationModel['stock_location'] = $warehouse;
            }

            if($stockLocationModel['stock_location']=='Nerul Warehouse' || $stockLocationModel['stock_location']=='Kurla Warehouse' || $stockLocationModel['stock_location']=='Bhiwandi Warehouse' || $stockLocationModel['stock_location']=='Andheri HO' || $stockLocationModel['stock_location']=='Bhiwandi EB-REPAIR CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-SERVICE CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-Open Units' || $stockLocationModel['stock_location']=='Bhiwandi EB-Warranty stock' || $stockLocationModel['stock_location']=='Bhiwandi EB-Refurbished' || $stockLocationModel['stock_location']=='Amazon BB – ASIS-Bhiwandi'|| $stockLocationModel['stock_location']=='Amazon BB – QC-Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – ASIS- Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – QC-Bhiwandi'){
                /*
                $warehouseAddress[0] = 'AMIABLE ELECTRONICS PVT LTD';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                $warehouseAddress[1] = 'C/O PROCONNECT SUPPLY CHAIN SOLUTIONS';       //MANDATORY FIELD (Pickup location wise address)
                $warehouseAddress[2] = 'LIMITED,BUL NO. D, GALA NO 14 15';      //MANDATORY FIELD  (Same Address) 
                $warehouseAddress[3] = 'Mumbai';      //MANDATORY FIELD (CITY)
                $warehouseAddress[4] = '421302';       //MANDATORY FIELD (PIN Code)
                $warehouseAddress[5] = 'Maharashtra';      //MANDATORY FIELD (State)
                $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                $warehouseAddress[7] = '9325887623';    //phone number
                $warehouseAddress[8] = '9325887623';     //MANDATORY FIELD (Mob. No)
                $warehouseAddress[9] = 'gautam.jadhav@proconnect.co.in';      //      (Email ID)*/
                /*$warehouseAddress[0] = 'AMIABLE ELECTRONICS PVT LTD';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                $warehouseAddress[1] = 'C/o DHL SUPPLY CHAIN I PVT LTD K Square';       //MANDATORY FIELD (Pickup location wise address)
                $warehouseAddress[2] = 'The Integrated Park Kurund NH3 Mumbai Nashik Highway';      //MANDATORY FIELD  (Same Address) 
                $warehouseAddress[3] = 'Bhiwandi';      //MANDATORY FIELD (CITY)
                $warehouseAddress[4] = '421302';       //MANDATORY FIELD (PIN Code)
                $warehouseAddress[5] = 'Maharashtra';      //MANDATORY FIELD (State) 
                $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                $warehouseAddress[7] = '9167610114';    //phone number
                $warehouseAddress[8] = '9167610114';     //MANDATORY FIELD (Mob. No)
                $warehouseAddress[9] = 'yogesh.lokhande@dhl.com';      //      (Email ID)
                $warehouseAddress[10] = 'istopay';      //      (Email ID)*/

		$warehouseAddress[0] = 'AMIABLE ELECTRONICS PVT LTD';       //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                $warehouseAddress[1] = 'MK SHAH EXPORTS';       //MANDATORY FIELD (Pickup location wise address)
                $warehouseAddress[2] = '2nd Floor, Plot No. D-141, TTC Industrial area, MIDC Shirawane';      //MANDATORY FIELD  (Same Address) 
                $warehouseAddress[3] = 'Nerul';      //MANDATORY FIELD (CITY)
                $warehouseAddress[4] = '400706';       //MANDATORY FIELD (PIN Code)
                $warehouseAddress[5] = 'Maharashtra';      //MANDATORY FIELD (State) 
                $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                $warehouseAddress[7] = '7219873385';    //phone number
                $warehouseAddress[8] = '7219873385';     //MANDATORY FIELD (Mob. No)
                $warehouseAddress[9] = 'anshul.singh@ingrammicro.com';      //      (Email ID)
                $warehouseAddress[10] = 'istopay';      //      (Email ID)


            }elseif($stockLocationModel['stock_location']=='Bangalore YCH HUB' || $stockLocationModel['stock_location']== 'Bangalore Proconnect HUB' || $stockLocationModel['stock_location']== 'Amazon BB – ASIS-Bangalore'|| $stockLocationModel['stock_location']== 'Amazon BB – QC-Bangalore'|| $stockLocationModel['stock_location']== 'FlipKart Prexo – QC-Bangalore'|| $stockLocationModel['stock_location']== 'FlipKart Prexo – ASIS-Bangalore'){ 
                   
                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'C/O Proconnet Supply Chain Solutions Ltd';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'Survey No. 18 and 22/4 Nagroor Village';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Bangalore';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '562123';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Karnakata';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9620996666';    //phone number  
                    $warehouseAddress[8] = '9620996666';     //MANDATORY FIELD (Mob. No)    
                	$warehouseAddress[9] = 'gautam.jadhav@proconnect.co.in';      //      (Email ID)
                    
                }elseif($stockLocationModel['stock_location']=='MP Warehouse'){

                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'M/s.Astha Industrial Company';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'Plot. No.2 3, Sector C, Industrial Area';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Bhopal';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '462023';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'MP';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9301120621';    //phone number  
                    $warehouseAddress[8] = '9301120621';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'sunand.kumar@proconnect.co.in';      //      (Email ID)  
                    
                }elseif($stockLocationModel['stock_location']=='RJ Warehouse'){
                    
                }elseif($stockLocationModel['stock_location']=='OR Warehouse'){

                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'PLOT NO:371, HIGHWAY COMPLEX';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'RUDRAPUR, NH5, BHUBANESHWAR';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Bhubaneshwar';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '752101';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'MP';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9778746967';    //phone number  
                    $warehouseAddress[8] = '9778746967';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'ranjankumar.b@proconnect.co.in';      //      (Email ID)  
                    
                }elseif($stockLocationModel['stock_location']=='AS Warehouse'){

                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'ProConnect Supply Chain Solutions Limited';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'NH37, Tetelia, 781035, Guwahati, Assam';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Assam';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '781035';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Assam';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9508008373';    //phone number  
                    $warehouseAddress[8] = '9508008373';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'manab.sarma@proconnect.co.in';      //      (Email ID)                                                                
                    
                }elseif($stockLocationModel['stock_location']=='CG Warehouse'){

                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'ProConnect Supply Chain Solutions Limited';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'Plot No # 06, Near Techno Industries';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Raipur';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '492001';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Chattishgad';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '09329100748';    //phone number  
                    $warehouseAddress[8] = '09329100748';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'tikelal.p@proconnect.co.in';      //      (Email ID)
                    
                }elseif($stockLocationModel['stock_location']=='Hyderabad Warehouse'){

                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'SHED NO 6, SREEKANTH REDDY ESTATE SY NO 91';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'KOMPALLY VILLAGE, QUTHBULLAPUR MANDAL, RANGAREDDY';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'TELANGANA';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '5OOO14';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Hyderabad';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9397112215';    //phone number  
                    $warehouseAddress[8] = '9397112215';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'shankar.kandula@proconnect.co.in';      //      (Email ID)
                   
                }elseif($stockLocationModel['stock_location']=='Uttar Pradesh Warehouse'){

                    $warehouseAddress[0] = 'Amiable Electronics Pvt. Ltd';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'KHASRA NO 357 VILL CHHAJARSI NOIDA';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'KHASRA NO 357 VILL CHHAJARSI NOIDA';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'NOIDA';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '201301';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Uttar Pradesh';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India) 
                    $warehouseAddress[7] = '9312874148';    //phone number  
                    $warehouseAddress[8] = '9312874148';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'Sunil.sharma@proconnect.co.in';      //      (Email ID)
                   
                }elseif($stockLocationModel['stock_location']=='Tamilnadu Warehouse'){

                    $warehouseAddress[0] = 'AMIABLE ELECTRONICS PRIVATE LIMITED';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    // $warehouseAddress[0] = 'DHL Supply Chain India Pvt. Ltd.';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'Survey nos  389, 400 2a, 400 2c, Padur Road';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'Kuthambakkam Village, Poonamalee taluk';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Thiruvallur';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '600124';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Tamilnadu';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '7823982455';    //phone number  
                    // $warehouseAddress[8] = '9380141516';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[8] = '7823982455';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'daniel.A@dhl.com';      //      (Email ID)       
                    
                }elseif($stockLocationModel['stock_location']=='Delhi Warehouse'){
                    
                    $warehouseAddress[0] = 'AMIABLE ELECTRONICS PRIVATE LIMITED';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'B 60 OKHALA INDUSTRIAL AREA PHASE 1 OKHLA Delhi';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'OKHLA  110020';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Delhi';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '110020';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Delhi';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9313104493';    //phone number  
                    $warehouseAddress[8] = '9313104493';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'pramodkumar.j@proconnect.co.in';      //      (Email ID)   

                }
                elseif($stockLocationModel['stock_location']=='Himachal Pradesh Warehouse'){
                    
                    $warehouseAddress[0] = 'AMIABLE ELECTRONICS PRIVATE LIMITED';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'C/O M/S Proconnect Supply Chain Solutions Limited';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = '12a,Sector 3,Parwanoo,Distt. Solan H.P 173220';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Himachal Pradesh';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '173220';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Himachal Pradesh';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9318687099';    //phone number  
                    $warehouseAddress[8] = '9318687099';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'amit.nagar@proconnect.co.in';      //      (Email ID)

                }
                elseif($stockLocationModel['stock_location']=='Haryana Warehouse'){
                   
                    $warehouseAddress[0] = 'AMIABLE ELECTRONICS PRIVATE LIMITED';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = '2916,Kataria Potteries complex,Daultabad, Nr RLY STn Gurgaon West';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'Gurgaon West';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Gurgaon';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '122001';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Gurgaon';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9350994343';    //phone number  
                    $warehouseAddress[8] = '9350994343';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'rakesh.chaudhary@proconnect.co.in';      //      (Email ID)

                }
                elseif($stockLocationModel['stock_location']=='Kolkata Warehouse'){ 
                    
                }
                else{
                         
                } 

            return $warehouseAddress;
		}
	}
?>
