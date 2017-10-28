<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php

class Plugincompany_Productshippingultimate_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{

    /**
     * Returns product attributes for installer
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
				'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes'        => array(
                    'pcu_shipping_per_country' => array(
						'group'				=> 'Shipping Rates Ultimate',
                        'label'             => 'Product Shipping Rates per Country and Customer Group',
                        'type'              => 'text',
                        'input'             => 'pcshippingultimate',
                        'default'           => '0',
                        'class'             => '',
                        'backend'           => '',
                        'frontend'          => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => false,
                        'unique'            => false,
                        'note'              => 'The product shipping rates per country and customer group configured above will override the default shipping rates.'
                    ),
					'pcu_per_item' => array(
						'group'				=> 'Shipping Rates Ultimate',
                        'label'             => 'Product Shipping Rate Calculation',
                        'type'              => 'int',
                        'default'           => '0',
                        'class'             => '',
                        'backend'           => 'eav/entity_attribute_backend_array',
                        'frontend'          => '',
                        'source'            => 'plugincompany_productshippingultimate/source_peritem',
						'input'				=> 'select',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,				
                        'required'          => false,
                        'user_defined'      => false,
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => false,
                        'unique'            => false,
						'note'				=> ' <b>Per item:</b> shipping rate is multiplied with the ordered quantity.<br/><b>Once per product:</b> shipping rate is charged once, regardless of the ordered quantity.<br/><b>Once per preset:</b> shipping rate is charged once for ordered products with the same preset.',
                    ),
					
					'pcu_override' => array(
						'group'				=> 'Shipping Rates Ultimate',
                        'label'             => 'Total Shipping Costs Calculation',
                        'type'              => 'int',
                        'default'           => '0',
                        'class'             => '',
						'backend'           => 'eav/entity_attribute_backend_array',
                        'frontend'          => '',
                        'source'            => 'plugincompany_productshippingultimate/source_override',
						'input'				=> 'select',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => false,
                        'unique'            => false,
						'note'				=> '<b>Add to total shipping costs:</b> The shipping costs for each item are added to the total shipping costs.<br /><b>Override total shipping costs:</b> product shipping costs will override the total shipping costs.',
                    ),
               )
           ),

      );
}

}