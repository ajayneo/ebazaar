<?php
$this->startSetup();
$table = $this->getConnection()
		->newTable($this->getTable('neo_notification/pushnotification'))
		->addColumn(
			'entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity' => true,
			'nullable' => false,
			'primary' => true,
				), 'ID'
		)
		->addColumn('device_type', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
	        'nullable'  => false,
	        ), 'Device Type'
        )
		->addColumn(
				'device_Id', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
				'nullable' => false,
				), 'Device ID'
		)
		->addColumn(
				'user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'nullable' => false,
				), 'User ID'
		)
		->addColumn(
				'status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(), 'Enabled'
		)
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        	), 'Created Time'
        )
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        	), 'Updated Time'
        )
		->setComment('PushNotification Table');
$this->getConnection()->createTable($table);

$this->endSetup();