<?php
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('postcode/postcode'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
		'identity'	=> true,
		'nullable'	=> false,
		'primary'	=> true,
		), 'Postcode Id')
	->addColumn('area', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(
		'nullable'	=> false,
		), 'Area Name')
	->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(
		'nullable'	=> false,
		), 'City Name')
	->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(
		'nullable'	=> false,
		), 'state Name')
	->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
		'nullable'	=> false,
		), 'Postcode')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(),'Status')
	->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP,null,array(),'Pincode Update Time')
	->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP,null,array(),'Pincode Create Time')
	->setComment('Pincode Manage Table');
$this->getConnection()->createTable($table);
$this->endSetup();
?>
