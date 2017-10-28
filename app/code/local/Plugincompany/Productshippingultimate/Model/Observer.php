<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Model_Observer
{
    /** @var Mage_Sales_Model_Quote_Address */
    private $quoteAddress;

    /** @var Mage_Sales_Model_Resource_Quote_Address_Rate_Collection */
    private $shippingRates;

    /** @var Mage_Sales_Model_Quote_Address_Rate */
    private $ultimateMethod;

    public function salesQuoteAddressCollectTotalsAfter(Varien_Event_Observer $observer)
    {
        $this->quoteAddress = $observer->getEvent()->getQuoteAddress();

        if($this->canDisableShippingMethods()){
            $this->updateShippingMethodAvailability();
        }
    }

    private function canDisableShippingMethods()
    {
        if(!$this->isDisablingMethodsEnabled()){
            return false;
        }
        if(!$this->getShippingRates()) {
            return false;
        }
        if(!$this->isShippingUltimateAvailable()){
            return false;
        }
        return true;
    }

    private function isDisablingMethodsEnabled()
    {
        return Mage::getStoreConfigFlag('carriers/productshippingultimate/disable_methods');
    }

    private function getShippingRates()
    {
        if(!$this->shippingRates){
            $this->initShippingRates();
        }
        return $this->shippingRates;
    }

    private function initShippingRates()
    {
        $this->shippingRates = $this->quoteAddress->getShippingRatesCollection();
        return $this;
    }

    private function isShippingUltimateAvailable()
    {
        return !is_null($this->getUltimateMethod());
    }

    private function getUltimateMethod()
    {
        if (!$this->hasUltimateMethod()) {
            $this->initUltimateMethod();
        }
        return $this->ultimateMethod;
    }

    private function hasUltimateMethod()
    {
        return isset($this->ultimateMethod);
    }

    private function initUltimateMethod()
    {
        $this->ultimateMethod = $this->shippingRates
            ->getItemByColumnValue('method', 'productshippingultimate');
        return $this;
    }

    private function updateShippingMethodAvailability()
    {
        foreach($this->getShippingRates()->getItems() as $key => $rate){
            $this->removeShippingRateIfAllowed($rate, $key);
        }
        return $this;
    }

    private function removeShippingRateIfAllowed($rate, $key)
    {
        if($this->canRemoveShippingRate($rate)){
            $this->removeFromShippingCollectionByKey($key);
        }
        return $this;
    }

    private function canRemoveShippingRate($rate)
    {
        if(in_array($rate->getCode(), $this->getDisabledMethods())){
            return true;
        }
        return false;
    }

    private function getDisabledMethods()
    {
        $methods = Mage::getStoreConfig('carriers/productshippingultimate/methods_to_disable');
        return explode(',', $methods);
    }

    private function removeFromShippingCollectionByKey($key)
    {
        $this->getShippingRates()->removeItemByKey($key);
        return $this;
    }
}