<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Block_Preset_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $this
            ->initForm()
            ->addFields()
            ->addModelDataToForm()
        ;

        return parent::_prepareForm();
    }

    private function initForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        return $this;
    }

    private function addFields()
    {
        $fieldset = $this->getForm()
            ->addFieldset(
                "preset_form",
                array(
                    "legend" => $this->getHelper()->__("Shipping Rate Settings")
                )
            );

        $fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => $this->getHelper()->__('Preset Name'),
            'title' => $this->getHelper()->__('Preset Name'),
            'required' => true,
        ));

        $fieldset->addField('shipping_per_country', 'pcshippingultimate', array(
            'name' => 'shipping_per_country',
            'label' => $this->getHelper()->__('Preset Shipping Rates'),
            'title' => $this->getHelper()->__('Preset Shipping Rates'),
            'required' => true,
        ));

        $fieldset->addField('in_preset_products', 'hidden', array(
            'name' => 'in_preset_products',
            'label' => $this->getHelper()->__('In Preset products'),
            'title' => $this->getHelper()->__('In Preset products'),
            'required' => false
        ));

        $fieldset->addField('per_item', 'select', array(
            'label'     => $this->getHelper()->__('Shipping Rate Calculation'),
            'title'     => $this->getHelper()->__('Shipping Rate Calculation'),
            'name'      => 'per_item',
            'required' => true,
            'options'   => $this->getPerItemOptions()
        ));

        $fieldset->addField('override', 'select', array(
            'label'     => $this->getHelper()->__('Total Shipping Cost Calculation'),
            'title'     => $this->getHelper()->__('Total Shipping Cost Calculation'),
            'name'      => 'override',
            'required' => true,
            'options'   => $this->getOverrideOptions()
        ));

        return $this;
    }

    public function getHelper()
    {
        return Mage::helper('plugincompany_productshippingultimate');
    }

    private function getPerItemOptions()
    {
        return Mage::getModel('plugincompany_productshippingultimate/source_preset_peritem')
            ->toOptionArray(true);
    }

    private function getOverrideOptions()
    {
        return Mage::getModel('plugincompany_productshippingultimate/source_override')
            ->toOptionArray(true);
    }

    private function addModelDataToForm()
    {
        if(!$this->getModel()) {
            return $this;
        }

        $this->getModel()
             ->initCalculationSku();

        $this->getForm()
            ->setValues(
                $this->getModel()->getData()
            );
        return $this;
    }

    private function getModel()
    {
        return Mage::registry('current_preset');
    }

}
