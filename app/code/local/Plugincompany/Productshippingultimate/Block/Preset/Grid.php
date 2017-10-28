<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Block_Preset_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
         $this->_blockGroup      = 'plugincompany_productshippingultimate';
         $this->_controller      = 'preset_grid';
         $this->_headerText      = $this->__('Product Shipping Rate Preset Manager');
         $this->_addButtonLabel  = $this->__('Add New Preset');
         parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

}

