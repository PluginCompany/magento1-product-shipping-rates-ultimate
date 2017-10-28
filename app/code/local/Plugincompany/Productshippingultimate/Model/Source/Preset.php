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
class Plugincompany_Productshippingultimate_Model_Source_Preset extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Options for product page
     *
     * @return array
     */
    public function getAllOptions()
    {

        $options = Mage::getModel('plugincompany_productshippingultimate/preset')->getCollection()->toOptionArray();

        return $options;
    }

    /**
     * Option array for config dropdown
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = Mage::getModel('plugincompany_productshippingultimate/preset')->getCollection()->toOptionHash();

        $options[0] = "Don't use a preset";

        return $options;
    }
}
