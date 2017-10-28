<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Model_Source_System_Config_ShippingMethods
    extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods
{

    public function toOptionArray()
    {
        $result = parent::toOptionArray();
        unset($result['productshippingultimate']);
        return $result;
    }

}
