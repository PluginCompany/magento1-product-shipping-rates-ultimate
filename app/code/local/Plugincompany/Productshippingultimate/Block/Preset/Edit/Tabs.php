<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Productshippingultimate_Block_Preset_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("pcshippingultimate_preset_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("plugincompany_productshippingultimate")->__("Preset Information"));
		}
		protected function _beforeToHtml()
		{
            $this->addTab("form_section", array(
            "label" => Mage::helper("plugincompany_productshippingultimate")->__("Shipping Rate Settings"),
            "title" => Mage::helper("plugincompany_productshippingultimate")->__("Shipping Rate Settings"),
            "content" => $this->getLayout()->createBlock("plugincompany_productshippingultimate/preset_edit_tab_form")->toHtml(),
            ));

            $this->addTab("products_section", array(
                "label" => Mage::helper("plugincompany_productshippingultimate")->__("Preset Products"),
                "title" => Mage::helper("plugincompany_productshippingultimate")->__("Preset Products"),
                "content" => $this->getLayout()->createBlock("plugincompany_productshippingultimate/preset_edit_tab_product")->toHtml(),
            ));

//            $this->addTab("conditions", array(
//                "label" => Mage::helper("groupswitch")->__("Rule Conditions"),
//                "title" => Mage::helper("groupswitch")->__("Rule Conditions"),
//                "content" => $this->getLayout()->createBlock("groupswitch/adminhtml_groupswitch_edit_tab_conditions")->toHtml(),
//            ));
//
//            $this->addTab("actions", array(
//                "label" => Mage::helper("groupswitch")->__("Rule Action"),
//                "title" => Mage::helper("groupswitch")->__("Rule Action"),
//                "content" => $this->getLayout()->createBlock("groupswitch/adminhtml_groupswitch_edit_tab_actions")->toHtml(),
//            ));
				return parent::_beforeToHtml();
		}

}
