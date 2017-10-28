/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */

var DisablePreset = Class.create({
    fields: '#pcu_per_item, #pcu_shipping_per_country, #pcu_override',
    presetStatusListener: function() {
        var that = this;
        jQuery('#pcu_preset_id').change(function(){
            that.updateShippingOptions();
        })
        return this;
    },
    updateShippingOptions: function() {
        if(this.isPresetEnabled()){
            return this.disableShippingOptions();
        }
        return this.enableShippingOptions();
    },
    isPresetEnabled: function() {
        return this.getPresetId() != 0;
    },
    getPresetId: function() {
        return jQuery('#pcu_preset_id').val();
    },
    disableShippingOptions: function() {
        var that = this;
        this.removeDisabledMask();
        jQuery(this.fields).each(function(){
            jQuery(this).closest('td')
                .css('position', 'relative')
                .prepend(that.newDisabledMask());
        });
        return this;
    },
    enableShippingOptions: function() {
        return this.removeDisabledMask();
    },
    removeDisabledMask: function() {
        jQuery('.pc-disable-mask').remove();
        return this;
    },
    newDisabledMask: function() {
        return jQuery('<div class="pc-disable-mask" />');
    }
});


jQuery(document).ready(function() {
    var disablePreset = new DisablePreset();
    disablePreset
        .updateShippingOptions()
        .presetStatusListener();
})