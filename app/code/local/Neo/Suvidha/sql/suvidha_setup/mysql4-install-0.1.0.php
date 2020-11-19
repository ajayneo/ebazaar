<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table creditsuvidha
(id int not null auto_increment, 
email_id varchar(100) NOT NULL,
company_name varchar(250) NOT NULL,
gst_no varchar(100) NOT NULL,
partner_details varchar(250) NOT NULL,
partnership_type varchar(100) NOT NULL,
billing_add varchar(250) NOT NULL,
billing_city varchar(100) NOT NULL,
billing_state varchar(150) NOT NULL,
billing_zip_code int(10) NOT NULL,
company_add varchar(250) NOT NULL,
company_city varchar(100) NOT NULL,
company_state varchar(100) NOT NULL,
company_zip_code int(10) NOT NULL,
credit_requested float NOT NULL,
business_commenced date NOT NULL,
nature_of_business varchar(100) NOT NULL,
bank_acc_name varchar(100) NOT NULL,
bank_acc_no int(50) NOT NULL,
bank_name varchar(100) NOT NULL,
bank_branch varchar(100) NOT NULL,
bank_acc_type varchar(50) NOT NULL,
bank_ifsc varchar(50) NOT NULL,
mobile int(12) NOT NULL,
aadhar varchar(200) NOT NULL,
pancard varchar(200) NOT NULL,
postcheque varchar(200) NOT NULL,
bankst varchar(200) NOT NULL,
ref_company1 varchar(150) NOT NULL,
ref_address1 varchar(250) NOT NULL,
ref_nature_of_business1 varchar(100) NOT NULL,
ref_phone1 int(12) NOT NULL,
ref_company2 varchar(150) DEFAULT NULL,
ref_address2 varchar(250) DEFAULT NULL,
ref_nature_of_business2 varchar(100) DEFAULT NULL,
ref_phone2 int(12) DEFAULT NULL,
sign_file1 varchar(250) NOT NULL,
sign_name1 varchar(100) NOT NULL,
sign_date1 date NOT NULL,
sign_file2 varchar(250) DEFAULT NULL,
sign_name2 varchar(100) DEFAULT NULL,
sign_date2 date DEFAULT NULL,
sign_file3 varchar(250) DEFAULT NULL,
sign_name3 varchar(100) DEFAULT NULL,
sign_date3 date DEFAULT NULL,
primary key(id));
SQLTEXT;

$installer->run($sql);
//demo 
Mage::getModel('suvidha/creditsuvidha')->setId(null);
//demo 
$installer->endSetup();
	 


