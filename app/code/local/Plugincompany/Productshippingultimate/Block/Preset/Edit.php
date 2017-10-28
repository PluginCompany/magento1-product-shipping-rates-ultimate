<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Block_Preset_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'preset_id';
        parent::__construct();
        $this->_blockGroup      = 'plugincompany_productshippingultimate';
        $this->_controller      = 'preset';
        $this->_mode            = 'edit';

        $modelTitle = $this->_getModelTitle();

        $this->_initPresetProducts();

        $this->_updateButton('save', 'label', $this->_getHelper()->__("Save $modelTitle"));
        $this->_addButton('saveandcontinue', array(
            'label'     => $this->_getHelper()->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        $json = $this->_getProductsJson();
        $this->_formScripts[] = "
               var presetProducts = \$H({" . $json . "});
    $('in_preset_products').value = presetProducts.toQueryString();

    function registerPresetProduct(grid, element, checked){
        if(checked){
                presetProducts.set(element.value);
        }
        else{
            presetProducts.unset(element.value);
        }
        $('in_preset_products').value = presetProducts.toQueryString();
        grid.reloadParams = {'selected_products[]':presetProducts.keys()};
    }
    function presetProductRowClick(grid, event){
        var trElement = Event.findElement(event, 'tr');
        var isInput   = Event.element(event).tagName == 'INPUT';
        if(trElement){
            var checkbox = Element.getElementsBySelector(trElement, 'input');
            if(checkbox[0]){
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                preset_productsJsObject.setCheckboxChecked(checkbox[0], checked);
            }
        }
    }

    var tabIndex = 1000;
    function presetProductRowInit(grid, row){
        var checkbox = $(row).getElementsByClassName('checkbox')[0];
    }


    preset_productsJsObject.rowClickCallback = presetProductRowClick;
    preset_productsJsObject.initRowCallback = presetProductRowInit;
    preset_productsJsObject.checkboxCheckCallback = registerPresetProduct;
    preset_productsJsObject.rows.each(function(row){presetProductRowInit(preset_productsJsObject, row)});
     preset_productsJsObject.reloadParams = {'selected_products[]':presetProducts.keys()};
        ";

    }

    protected function _getHelper(){
        return Mage::helper('plugincompany_productshippingultimate');
    }

    protected function _getModel(){
        return Mage::registry('current_preset');
    }

    protected function _getModelTitle(){
        return 'Shipping Preset';
    }

    protected function _initPresetProducts()
    {
        if ($this->_getModel()->getId()) {
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection
                ->addAttributeToSelect('pcu_preset_id')
                ->addAttributeToFilter('pcu_preset_id', $this->_getModel()->getId());
            $ids = array();
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
        }else{
            $ids = array();
        }

        Mage::register('current_preset_products', $ids);
        return $this;
    }

    protected function _getProductsJson()
    {
        if ($ids = Mage::registry('current_preset_products')) {
            $json = array();
            foreach ($ids as $id) {
                $json[] = $id . ':' . 0;
            }
            $json = implode(',', $json);
            return $json;
        }
    }

    public function getHeaderText()
    {
        $model = $this->_getModel();
        $modelTitle = $this->_getModelTitle();
        if ($model && $model->getId()) {
           return $this->_getHelper()->__("Edit $modelTitle (ID: {$model->getId()})");
        }
        else {
           return $this->_getHelper()->__("New $modelTitle");
        }
    }


    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    /**
     * Get form save URL
     *
     * @deprecated
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
                $this->setData('form_action_url', 'save');
                return $this->getFormActionUrl();
    }


}
