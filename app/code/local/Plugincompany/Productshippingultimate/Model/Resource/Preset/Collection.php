<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */ 
class Plugincompany_Productshippingultimate_Model_Resource_Preset_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('plugincompany_productshippingultimate/preset');
    }

    protected function _toOptionArray($valueField='preset_id', $labelField='title', $additional=array())
    {
        $options = parent::_toOptionArray($valueField, $labelField, $additional);
        array_unshift($options, array('label' => "Don't use a preset", 'value' => 0));
        return $options;
    }

    protected function _toOptionHash($valueField='preset_id', $labelField='title')
    {
       $options = parent::_toOptionHash($valueField, $labelField);
        $options[0] = "Don't use a preset";
        return $options;
    }


}