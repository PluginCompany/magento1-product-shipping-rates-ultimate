<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */ 
class Plugincompany_Productshippingultimate_Model_Resource_Preset extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('plugincompany_productshippingultimate/preset', 'preset_id');
    }


}