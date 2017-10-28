<?php

/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Model_Source_Preset_Peritem
    extends Plugincompany_Productshippingultimate_Model_Source_Peritem
{

    public function getAllOptions()
    {
        $options = parent::getAllOptions();
        $options[] =
            array(
                'label' => 'Once per preset',
                'value' => 3
            );
        return $options;
    }

}

