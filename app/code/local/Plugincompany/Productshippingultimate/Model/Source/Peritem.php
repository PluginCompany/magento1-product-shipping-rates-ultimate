<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php

/**
 * Class Plugincompany_Productshippingultimate_Model_Source_Peritem
 */
class Plugincompany_Productshippingultimate_Model_Source_Peritem
    extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Options for product page
     *
     * @return array
     */
    public function getAllOptions()
    {
        return array(
            array(
                'label' => 'Use default config',
                'value' => 0
            ),
            array(
                'label' => 'Per item (SKU*QTY)',
                'value' => 1
            ),
            array(
                'label' => 'Once per product (SKU)',
                'value' => 2
            )
        );
    }

    /**
     * Option array for config dropdown
     *
     * @return array
     */
    public function toOptionArray($showDefault = false)
    {
        $options = array();

        foreach ($this->getAllOptions() as $option) {
            //exclude use config value,
            //because optionarray is used for config
            if (!$showDefault) {
                if ($option['value'] == 0) {
                    continue;
                }
            }
            //add option
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
