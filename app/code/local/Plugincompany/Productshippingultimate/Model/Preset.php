<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */ 
class Plugincompany_Productshippingultimate_Model_Preset extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('plugincompany_productshippingultimate/preset');
    }

    protected function _afterSave()
    {
        $this
            ->removeProducts()
            ->addProducts()
        ;

        return parent::_afterSave();
    }

    protected function _afterLoad()
    {
        $this->initCalculationSku();
        return parent::_afterLoad();
    }

    public function initCalculationSku()
    {
        if(!$this->getCalculationSku()){
            $this->setCalculationSku($this->getNewCalculationSku());
        }
        return $this;
    }

    private function getNewCalculationSku()
    {
        if($this->getId()){
            return 'preset_id_' . $this->getId();
        }
        return 'preset_id_' . $this->getNextPresetId();
    }

    private function getNextPresetId()
    {
        return $this->getLastPresetId() + 1;
        return $this;
    }

    private function getLastPresetId()
    {
        return $this->getCollection()
            ->addOrder('preset_id', 'DESC')
            ->getFirstItem()
            ->getId();
    }

    public function removeProducts()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->addAttributeToSelect('pcu_preset_id')
            ->addAttributeToFilter('pcu_preset_id', $this->getId());

        $ids = array();

        $keep = $this->getInPresetProducts();
        $keep = explode('&', $keep);

        foreach ($collection as $item) {
            if (!in_array($item->getId(), str_replace('=0','',$keep))) {
                $ids[] = $item->getId();
            }
        }

        try {
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($ids, array('pcu_preset_id' => NULL), 0);
        } catch (Exception $e) {
            throw new Exception('Error removing products');
        }
        return $this;
    }

    public function addProducts()
    {
        $ids = $this->getInPresetProducts();
        if (!is_array($ids)) {
            $ids = explode('&', $ids);
        }

        $idds = array();
        foreach ($ids as $id) {
            if (!$id || !is_numeric($id)) {
                continue;
            }
            $idds[] = $id;
        }

        try {
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($idds, array('pcu_preset_id'=>$this->getId()), 0);
        } catch (Exception $e) {
            throw new Exception('Error adding products');
        }
        return $this;
    }


}