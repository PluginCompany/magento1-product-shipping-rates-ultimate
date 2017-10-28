<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('plugincompany_productshippingultimate/preset');
$sql="
drop table if exists {$table};
create table {$table}(
    preset_id int not null auto_increment,
    title varchar(255),
    per_item int(10),
    override int(10),
    shipping_per_country text,
    primary key(preset_id)
);
";

$installer->run($sql);

$installer->addAttribute('catalog_product', 'pcu_preset_id', array(         // TABLE.COLUMN:                                       DESCRIPTION:
    'group'				=> 'Shipping Rates Ultimate',
    'label'             => 'Use Preset',
    'sort_order'        => -1,
    'type'              => 'int',
    'default'           => '0',
    'class'             => '',
    'backend'           => 'eav/entity_attribute_backend_array',
    'frontend'          => '',
    'source'            => 'plugincompany_productshippingultimate/source_preset',
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
    'note'				=> 'Selected presets will take effect after saving the product. Alternatively, product shipping rates can be configured manually below.',
));


$installer->endSetup();
?>