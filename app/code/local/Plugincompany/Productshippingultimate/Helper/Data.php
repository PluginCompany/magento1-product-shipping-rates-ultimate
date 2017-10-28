<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Productshippingultimate_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve shipping rate for product for a specific country
     *
     * @param $_product
     * @param null $countryId
     * @return int|string
     */
    public function getShippingRate($_product, $countryId = null)
    {
        if (!$_product->getId()) {
            return 'Error retrieving shipping rate';
        }
        if (!$_product->getMbPresetId() || $_product->getMbShippingPerCountry()) {
            $_resource = $_product->getResource();
            $presetId = $_resource->getAttributeRawValue($_product->getId(), 'mb_preset_id', Mage::app()->getStore());
            $shippingRates = $_resource->getAttributeRawValue($_product->getId(), 'mb_shipping_per_country', Mage::app()->getStore());
            $_product->setMbPresetId($presetId);
            $_product->setMbShippingPerCountry($shippingRates);
        }
        return Mage::getModel('plugincompany_productshippingultimate/carrier_productshippingultimate')->getShippingRateForProduct($_product, $countryId);
    }
}
	 