<?php
class Neo_CustomFields_Model_Entity_Setup extends Mage_Customer_Model_Entity_Setup
{
	public function getDefaultEntities()
    {
        $entities = array(
            'customer'                       => array(
                'entity_model'                   => 'customer/customer',
                'attribute_model'                => 'customer/attribute',
                'table'                          => 'customer/entity',
                'increment_model'                => 'eav/entity_increment_numeric',
                'additional_attribute_table'     => 'customer/eav_attribute',
                'entity_attribute_collection'    => 'customer/attribute_collection',
                'attributes'                     => array(
                    'mobile'              => array(
                        'type'               => 'static',
                        'label'              => 'Mobile',
                        'visible'			=> true,
                        'required'			=> false,
                        'sort_order'         => 80,
                    ),
                    'landmark'              => array(
                        'type'               => 'varchar',
                        'label'              => 'Landmark',
                        'visible'			=> true,
                        'required'			=> true,
                        'sort_order'         => 80,
                    ),
                    'cus_country'           => array(
                        'type'              => 'varchar',
                        'input'         	=> 'select',
                        'label'             => 'Country',
                        'class'				=> 'countries',
                        'source'        	=> 'customfields/selectoptions',
                        'visible'			=> true,
                        'required'			=> true,
                        'sort_order'        => 80,
                    ),
                    'cus_state'           => array(
                        'type'              => 'varchar',
                        'input'         	=> 'select',
                        'label'             => 'State',
                        'class'				=> 'state',
                        'source'        	=> 'customfields/selectstates',
                        'visible'			=> true,
                        'required'			=> true,
                        'sort_order'        => 80,
                    ),
                    'cus_city'           => array(
                        'type'              => 'varchar',
                        'input'         	=> 'text',
                        'label'             => 'City',
                        'class'				=> 'city',
                        'visible'			=> true,
                        'required'			=> true,
                        'sort_order'        => 80,
                    ),
                    'cus_telephone'              => array(
                        'type'               => 'static',
                        'input'         	=> 'text',
                        'label'              => 'Telephone',
                        'visible'			=> true,
                        'required'			=> false,
                        'sort_order'         => 80,
                    ),
                )
            )
        );
        return $entities;
    }
}
?>