<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */

/**
 * class Plugincompany_Productshippingultimate_Model_Carrier_Productshippingultimate
 */
class Plugincompany_Productshippingultimate_Model_Carrier_Productshippingultimate
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    const FALLBACK_CUSTOMER_GROUP = '-1';
    const FALLBACK_COUNTRY = '-';

    const CALCULATION_PER_ITEM = '1';
    const CALCULATION_PER_SKU = '2';
    const CALCULATION_PER_PRESET = '3';

    const OVERRIDE_TOTAL = '2';
    const ADD_TO_TOTAL = '1';

    /**
     * @var string
     */
    protected $_code = 'productshippingultimate';

    /**
     * @var mage_shipping_model_rate_request
     */
    private $request;
    private $perOrderTotal = 0;
    private $perOrderOverride = 0;
    private $perOrderOverrideCount = 0;
    private $currentItem;
    private $currentProduct;
    private $destinationCountryId;
    private $presetRatesAdded = array();
    private $currentPreset;

    /**
     * Add method to allowed methods
     *
     * @return array
     */

    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->request = $request;

        if (!$this->isAvailable()) {
            return false;
        }

        if ($this->getRequest()->getFreeShipping() === true) {
            return $this->generateResult();
        }

        foreach ($this->getRequest()->getAllItems() as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }
            $this
                ->initCurrentItem($item)
                ->addCurrentProductRateToTotal()
                ->markPresetAsProcessed()
            ;
        }

        return $this->generateResult();

    }

    private function isAvailable()
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        if (!$this->getRequest()->getAllItems()) {
            return false;
        }
        return true;
    }

    private function getRequest()
    {
        if ($this->request) {
            return $this->request;
        }
        return new Varien_Object();
    }

    private function initCurrentItem($item)
    {
        $this->currentItem = $item;
        $this->initCurrentProduct();
        $this->initCurrentPreset();
        return $this;
    }

    private function initCurrentProduct()
    {
        $this->setCurrentProduct(
            $this->getProductFromShippingItem($this->currentItem)
        );
        return $this;
    }

    private function setCurrentProduct($product)
    {
        $this->currentProduct = $product;
        return $this;
    }

    /**
     * @param $item
     * @return Mage_Catalog_Model_Product
     */
    private function getProductFromShippingItem($item)
    {
        return Mage::getModel('catalog/product')->load($item->getProduct()->getId());
    }

    private function initCurrentPreset()
    {
        if(!$this->isCurrentProductUsingPreset()) {
            return $this->resetCurrentPreset();
        }
        $this->currentPreset = $this->getPreset(
            $this->currentProduct->getPcuPresetId()
        );
        return $this;
    }

    private function resetCurrentPreset()
    {
        $this->currentPreset = false;
        return $this;
    }

    /**
     * @param $presetId
     * @return Plugincompany_Productshippingultimate_Model_Preset
     */
    private function getPreset($presetId)
    {
        return Mage::getSingleton('plugincompany_productshippingultimate/preset')->load($presetId);
    }

    private function addCurrentProductRateToTotal()
    {
        if($this->isCurrentPresetRateAlreadyAdded()){
            return $this;
        }
        if ($this->getOverrideSettingForProduct() == self::OVERRIDE_TOTAL) {
            return $this->addProductRateToOverrideTotal();
        }
        return $this->addProductRateToRegularTotal();
    }

    private function isCurrentPresetRateAlreadyAdded()
    {
        if(!$this->isCurrentProductUsingPreset()){
            return false;
        }
        if(in_array($this->currentPreset->getPresetId(), $this->presetRatesAdded)){
            return true;
        }
        return false;
    }

    private function isCurrentProductUsingPreset()
    {
        return (bool)$this->currentProduct->getPcuPresetId();
    }

    private function addProductRateToOverrideTotal()
    {
        $this->perOrderOverrideCount++;

        $rate = $this->getCountryRateForCurrentItem();

        if ($this->getPerItemSettingForProduct() === self::CALCULATION_PER_ITEM) {
            $rate = $rate * $this->currentItem->getQty();
        }

        if ($rate > $this->perOrderOverride) {
            $this->perOrderOverride = $rate;
        }

        return $this;
    }

    private function addProductRateToRegularTotal()
    {
        $rate = $this->getCountryRateForCurrentItem();

        if ($this->getPerItemSettingForProduct() == self::CALCULATION_PER_ITEM) {
            $rate = $rate * $this->currentItem->getQty();
        }

        $this->perOrderTotal += $rate;

        return $this;
    }

    private function getCountryRateForCurrentItem()
    {
        if (!$this->currentItem->getFreeShipping()) {
            return $this->getShippingRateForCurrentProduct();
        }
        return 0;
    }


    public function getShippingRateForProduct(
        Mage_Catalog_Model_Product $product,
        $countryId = null
    ) {
        if ($product) {
            $this->setCurrentProduct($product);
        }
        if ($countryId) {
            $this->setDestinationCountryId($countryId);
        }
        return $this->getShippingRateForCurrentProduct();
    }

    /**
     * Get product specific rate for country
     * @return float
     */
    private function getShippingRateForCurrentProduct()
    {
        $rate = $this->getRateForProduct();
        if ($rate !== false) {
            return $rate;
        }

        $defaultRate = $this->getDefaultRate();
        if ($defaultRate !== false) {
            return $defaultRate;
        }

        return 0;
    }

    private function getOverrideSettingForProduct()
    {
        if ($this->isCurrentProductUsingPreset()) {
            return $this->getPresetOverrideSetting();
        }
        if ($this->currentProduct->getPcuOverride()) {
            return $this->currentProduct->getPcuOverride();
        }
        return $this->getConfigData('pcu_standard_override');
    }

    private function getPresetOverrideSetting()
    {
        return $this->currentPreset
            ->getOverride();
    }

    private function getPerItemSettingForProduct()
    {
        if ($this->isCurrentProductUsingPreset()) {
            return $this->getPresetPerItemSetting();
        }
        if ($this->currentProduct->getPcuPerItem()) {
            return $this->currentProduct->getPcuPerItem();
        }
        return $this->getConfigData('pcu_standard_per_item');
    }

    private function getPresetPerItemSetting()
    {
        return $this->currentPreset->getPerItem();
    }


    private function getCurrentCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    private function getDestinationCountryId()
    {
        if ($this->destinationCountryId) {
            return $this->destinationCountryId;
        }
        return $this->getRequest()->getDestCountryId();
    }

    private function setDestinationCountryId($id)
    {
        $this->destinationCountryId = $id;
        return $this;
    }

    private function getRateForProduct()
    {
        return $this->getRateFromRatesData(
            $this->getRatesForProduct($this->currentProduct)
        );
    }

    private function getRateFromRatesData(array $ratesData)
    {
        if ($this->getCustomerGroupCountryRate($ratesData) !== false) {
            return $this->getCustomerGroupCountryRate($ratesData);
        }
        if ($this->getFallbackCustomerGroupCountryRate($ratesData) !== false) {
            return $this->getFallbackCustomerGroupCountryRate($ratesData);
        }
        if ($this->getCustomerGroupFallbackCountryRate($ratesData) !== false) {
            return $this->getCustomerGroupFallbackCountryRate($ratesData);
        }
        if ($this->getGeneralFallbackRate($ratesData) !== false) {
            return $this->getGeneralFallbackRate($ratesData);
        }
        return false;
    }

    private function getCustomerGroupCountryRate($ratesData)
    {
        return $this->getRateValueFromData(
            $ratesData,
            $this->getCurrentCustomerGroupId() . $this->getDestinationCountryId()
        );
    }

    private function getFallbackCustomerGroupCountryRate($ratesData)
    {
        return $this->getRateValueFromData(
            $ratesData,
            self::FALLBACK_CUSTOMER_GROUP . $this->getDestinationCountryId()
        );
    }

    private function getCustomerGroupFallbackCountryRate($ratesData)
    {
        return $this->getRateValueFromData(
            $ratesData,
            $this->getCurrentCustomerGroupId() . self::FALLBACK_COUNTRY
        );
    }

    private function getGeneralFallbackRate($ratesData)
    {
        return $this->getRateValueFromData(
            $ratesData,
            self::FALLBACK_CUSTOMER_GROUP . self::FALLBACK_COUNTRY
        );
    }

    private function getRateValueFromData($ratesData, $key)
    {
        if (array_key_exists($key, $ratesData)) {
            return $ratesData[$key];
        }
        return false;
    }

    private function getRatesForProduct()
    {
        if ($this->isCurrentProductUsingPreset()) {
            return $this->getPresetRates();
        }
        return $this->getProductSpecificRates();
    }

    private function getPresetRates()
    {
        return $this->getRatesData(
            $this->currentPreset->getShippingPerCountry()
        );
    }

    private function getProductSpecificRates()
    {
        return $this->getRatesData(
            $this->currentProduct->getPcuShippingPerCountry()
        );
    }

    private function getDefaultRate()
    {
        return $this->getRateFromRatesData(
            $this->getDefaultRates()
        );
    }

    private function getDefaultRates()
    {
        return $this->getRatesData(
            $this->getConfigData('pcu_standard_rate')
        );
    }

    /**
     * Creates array from customer group, country & rate database string
     *
     * @param $values
     * @return array|bool
     */
    private function getRatesData($values)
    {
        $var = json_decode($values);

        $values = array();
        if ($var) {
            foreach ($var as $v) {
                $values[$v[0] . $v[1]] = $v[2];
            }
        }

        return $values;
    }

    private function markPresetAsProcessed()
    {
        if(!$this->productHasPresetRate()){
            return $this;
        }
        if($this->getPreset()->getPerItem() != self::CALCULATION_PER_PRESET){
            return $this;
        }
        $this->presetRatesAdded[] = $this->getPreset()->getPresetId();
        return $this;
    }
    private function productHasPresetRate()
    {
        if (!$this->isCurrentProductUsingPreset()) {
            return false;
        }
        return (bool)$this->getRateFromRatesData(
            $this->getPresetRates()
        );
    }


    private function generateResult()
    {
        if(!$this->canShowResult()){
            return false;
        }
        $result = Mage::getModel('shipping/rate_result');
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice($this->getTotal());
        $method->setCost($this->getTotal());
        $result->append($method);
        return $result;
    }

    private function canShowResult()
    {
        if($this->getTotal() == 0 && $this->hideIfTotalIsZero()){
            return false;
        }
        return true;
    }

    private function getTotal()
    {
        if ($this->getRequest()->getFreeShipping() === true) {
            return 0;
        }
        if ($this->perOrderOverrideCount > 0) {
            return $this->perOrderOverride;
        }
        return $this->perOrderTotal;
    }

    private function hideIfTotalIsZero()
    {
        return $this->getConfigFlag('hide_if_zero');
    }




}

