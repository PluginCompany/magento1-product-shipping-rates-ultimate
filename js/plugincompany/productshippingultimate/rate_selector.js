
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */

function addshippingvaluesUltimate(){
    var pcshippingForm = new varienForm('plugshipu',true);
    var preset = new Array();

    if (!jQuery('#pcucountry').val() || jQuery('#pcucountry').val() == 0){
        jQuery('#pcucountry').addClass('required');
        return;
    } else{jQuery('#pcucountry').removeClass('required');}
    if (!jQuery('#pcuCustGroup').val()){
        jQuery('#pcuCustGroup').addClass('required');
        return;
    } else{jQuery('#pcuCustGroup').removeClass('required');}
    jQuery('#pcuprice').addClass('required-entry');

    if (isJSONarray(jQuery('.pcu_shipping_per_country').val())){
        preset.push(JSON.parse(jQuery('.pcu_shipping_per_country').val()));
        jQuery('.pcu_shipping_per_country').val(JSON.stringify(preset));
    }

    if ((pcshippingForm.validator.validate()) && (jQuery('#pcucountry').val() !== 0)) {
        jQuery('#pcucountry option:selected').each(function() {
            jQuery('#pcushippingtable tbody').append('<tr><td style="text-align:center;" >'+jQuery(this).val()
                +'</td><td >'+jQuery(this).text()+'</td><td>'
                +jQuery('#pcuCustGroup option:selected').text()+'</td><td style="text-align:right;" >'
                +parseFloat(jQuery('#pcuprice').val().replace(',','.')).toFixed(2)
                +'</td><td><button title="Delete row" type="button" '
                +'class="scalable delete icon-btn delete-product-option pcudelete" '
                +'onclick="pcudeletitem(this)"><span><span><span>Delete</span></span></span>'
                +'</button></td></tr>');

            var current = new Array(jQuery('#pcuCustGroup').val(),
                jQuery(this).val(),
                parseFloat(jQuery('#pcuprice').val().replace(',','.')).toFixed(2));
            if (preset){ preset.push(current); }else{preset=current;}
            jQuery('.pcu_shipping_per_country').val(JSON.stringify(preset).replace('[[[','[[').replace(']]]',']]').replace(']],','],'));
            delete(current);
        });
        jQuery('#pcuprice').val('');
    }
    jQuery('#pcuprice').removeClass('required-entry');
    checkoptions();
}

function pcGroupChange(){
    checkoptions();
}

function pcudeletitem(row){
    function deleteArrayByGroupAndCountry(source, group, code){
        var cgroup=""+group;
        return source.filter(function(obj){
            return (code+cgroup !== obj[1]+obj[0]);
        });
    }

    var preset = JSON.parse(jQuery('.pcu_shipping_per_country').val());
    var pcuval = jQuery(row).parent().siblings('td');
    var pcugroup = ""+getGroupId(pcuval.eq(2).text());
    var pcucountry = pcuval.eq(0).text();
    var preset = deleteArrayByGroupAndCountry(preset, pcugroup, pcucountry);
    jQuery('.pcu_shipping_per_country').val(JSON.stringify(preset));
    jQuery(row).parent().parent().remove();
    checkoptions();
}

function getCountryCode(cName){
    var rslt="";
    jQuery('#pcucountry option').each(function(){
        return jQuery(this).text()===cName?
            rslt = jQuery(this).val():((cName==="Default rate")? rslt="-": void 0);
    });
    return rslt;
}

function getGroupId(group){
    var rslt="";
    jQuery('#pcuCustGroup option').each(function(){
        return jQuery(this).text()===group?
            rslt = jQuery(this).val():((group==="All Groups")? rslt="-1": void 0);
    });
    return rslt;
}

function isJSONarray(jsonString){
    var isArray = (((jsonString.split('[')).length>1) && ((jsonString.split('[')).length === (jsonString.split(']')).length));
    var isEmpty = (jsonString === '[]');
    return (isArray && JSON.parse(jsonString) && !isEmpty);
    return false;
}

function checkoptions(){
    function findByGroupAndCountry(source, group, country){
        return source.filter(function(obj){
            return (obj[0]===group) && (obj[1]===country);
        })[0];
    }


    if (!isJSONarray(jQuery('.pcu_shipping_per_country').val())) {
        jQuery('.pcu_shipping_per_country').val('');
        return;
    }

    var preset=JSON.parse(jQuery('.pcu_shipping_per_country').val());
    var thisgroup = jQuery('#pcuCustGroup').val();
    jQuery('#pcucountry option').each(function(){
        var thisval = jQuery(this).val();
        if (findByGroupAndCountry(preset,thisgroup,thisval)){
            jQuery(this).hide();
        } else {
            jQuery(this).show(this);
        }
    });
}

jQuery(document).ready(function(){
    jQuery('#pcuprice').keyup(function(e) {
        if(e.keyCode === 13) {
            addshippingvaluesUltimate();
        }
    });
    checkoptions();
    addRegionButtonsUltimate();
});

function addRegionButtonsUltimate() {
    jQuery.each(pcuregions,function(k,v) {
        var button = '<button style="margin:3px;width:110px" type="button" onclick="selectRegionUltimate(\'' + k + '\')"><span>' + k.capitalize() + '</span></button>';
        jQuery(button).appendTo('#pcuregions');
    });
}

function selectRegionUltimate(region) {
    var reg = pcuregions[region];
    jQuery('#pcucountry option').prop('selected', false);
    jQuery.each(reg.countries,function(k,v) {
        jQuery('#pcucountry option[value="' + v + '"]').prop('selected', true);
    });
}

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

var pcuregions = {};

pcuregions.europe = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Europe
        'AL', // Albania
        'AD', // Andorra
        'AM', // Armenia
        'AT', // Austria
        'AZ', // Azerbaijan
        'BY', // Belarus
        'BE', // Belgium
        'BA', // Bosnia and Herzegovina
        'BG', // Bulgaria
        'HR', // Croatia
        'CY', // Cyprus
        'CZ', // Czech Republic
        'DK', // Denmark
        'EE', // Estonia
        'FI', // Finland
        'FR', // France
        'GE', // Georgia (country)
        'DE', // Germany
        'GR', // Greece
        'HU', // Hungary
        'IS', // Iceland
        'IE', // Republic of Ireland
        'IT', // Italy
        'KZ', // Kazakhstan
        'LV', // Latvia
        'LI', // Liechtenstein
        'LT', // Lithuania
        'LU', // Luxepcuourg
        'MK', // Republic of Macedonia
        'MT', // Malta
        'MD', // Moldova
        'MC', // Monaco
        'ME', // Montenegro
        'NL', // Netherlands
        'NO', // Norway
        'PL', // Poland
        'PT', // Portugal
        'RO', // Romania
        'RU', // Russia
        'SM', // San Marino
        'RS', // Serbia
        'SK', // Slovakia
        'SI', // Slovenia
        'ES', // Spain
        'SE', // Sweden
        'CH', // Switzerland
        'TR', // Turkey
        'UA', // Ukraine
        'GB', // United Kingdom
        'VA', // Vatican City

        // Several dependencies and similar territories with broad autonomy are also found in Europe. Note that the list does not include the constituent countries of the United Kingdom, federal states of Germany and Austria, and autonomous territories of Spain and the post-Soviet republics as well as the republic of Serbia.
        // Åland (Finland)
        'FO', // Faroe Islands (Denmark)
        'GI', // Gibraltar (UK)
        'GG', // Guernsey (UK)
        'IM', // Isle of Man (UK)
        'JE', // Jersey (UK)
        'SJ' // Svalbard and Jan Mayen (Norway)
    ]
};

pcuregions.middleEast = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Middle_east
        'BH', // Bahrain
        'CY', // Cyprus
        'EG', // Egypt
        'IR', // Iran
        'IQ', // Iraq
        'IL', // Israel
        'JO', // Jordan
        'KW', // Kuwait
        'LB', // Lebanon
        'OM', // Oman
        'PS', // Palestine
        'QA', // Qatar
        'SA', // Saudi Arabia
        'SY', // Syria
        'TR', // Turkey
        'AE', // United Arab Emirates
        'YE' // Yemen
    ]
};

pcuregions.latinAmerica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Latin_America
        'AR', // Argentina
        'BO', // Bolivia
        'BR', // Brazil
        'CL', // Chile
        'CO', // Colopcuia
        'CR', // Costa Rica
        'CU', // Cuba
        'DO', // Dominican Republic
        'EC', // Ecuador
        'SV', // El Salvador
        'GT', // Guatemala
        'HT', // Haiti
        'HN', // Honduras
        'MX', // Mexico
        'NI', // Nicaragua
        'PA', // Panama
        'PY', // Paraguay
        'PE', // Peru
        'PR', // Puerto Rico
        'UY', // Uruguay
        'VE' // Venezuela
    ]
};

pcuregions.southAmerica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/South_America
        'AR', // Argentina
        'BO', // Bolivia
        'BR', // Brazil
        'CL', // Chile
        'CO', // Colopcuia
        'EC', // Ecuador
        'FK', // Falkland Islands (United Kingdom)
        'GF', // French Guiana (France)
        'GY', // Guyana
        'PY', // Paraguay
        'PE', // Peru
        'GS', // South Georgia and the South Sandwich Islands
        'SR', // Suriname
        'TT', // Trinidad and Tobago
        'UY', // Uruguay
        'VE' // Venezuela
    ]
};

pcuregions.easternAfrica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Africa
        'BI', // Burundi
        'KM', // Comoros
        'DJ', // Djibouti
        'ER', // Eritrea
        'ET', // Ethiopia
        'KE', // Kenya
        'MG', // Madagascar
        'MW', // Malawi
        'MU', // Mauritius
        'YT', // Mayotte (France)
        'MZ', // Mozapcuique
        'RE', // Réunion (France)
        'RW', // Rwanda
        'SC', // Seychelles
        'SO', // Somalia
        'SS', // South Sudan
        'TZ', // Tanzania
        'UG', // Uganda
        'ZM', // Zapcuia
        'ZW' // Zipcuabwe
    ]
};

pcuregions.centralAfrica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Africa
        'AO', // Angola
        'CM', // Cameroon
        'CF', // Central African Republic
        'TD', // Chad
        'CG', // Republic of the Congo
        'CD', // Democratic Republic of the Congo
        'GQ', // Equatorial Guinea
        'GA', // Gabon
        'ST' // São Tomé and Príncipe
    ]
};

pcuregions.northernAfrica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Africa
        'DZ', // Algeria
        'IC', // Canary Islands (Spain)
        // Santa Cruz de Tenerife
        'EA', // Ceuta (Spain)
        'EG', // Egypt
        'LY', // Libya
        // Madeira (Portugal)
        // Melilla (Spain)
        'MA', // Morocco
        'SD', // Sudan
        'TN', // Tunisia
        'EH' // Western Sahara
    ]
};

pcuregions.southernAfrica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Africa
        'BW', // Botswana
        'LS', // Lesotho
        'NA', // Namibia
        'ZA', // South Africa
        'SZ' // Swaziland
    ]
};

pcuregions.westernAfrica = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Africa
        'BJ', // Benin
        'BF', // Burkina Faso
        'CV', // Cape Verde
        'CI', // Côte d'Ivoire
        'GM', // Gapcuia
        'GH', // Ghana
        'GN', // Guinea
        'GW', // Guinea-Bissau
        'LR', // Liberia
        'ML', // Mali
        'MR', // Mauritania
        'NE', // Niger
        'NG', // Nigeria
        'SH', // Saint Helena, Ascension and Tristan da Cunha (United Kingdom)
        'SN', // Senegal
        'SL', // Sierra Leone
        'TG' // Togo
    ]
};


pcuregions.asia = {
    countries: [
        // source is http://en.wikipedia.org/wiki/Asia
        'AF', // Afghanistan
        'AM', // Armenia
        'AZ', // Azerbaijan
        'BH', // Bahrain
        'BD', // Bangladesh
        'BT', // Bhutan
        'BN', // Brunei
        'KH', // Capcuodia
        'CN', // China
        'CY', // Cyprus
        'TL', // East Timor
        'GE', // Georgia (country)
        'IN', // India
        'ID', // Indonesia
        'IR', // Iran
        'IQ', // Iraq
        'IR', // Israel
        'JP', // Japan
        'JO', // Jordan
        'KZ', // Kazakhstan
        'KW', // Kuwait
        'KG', // Kyrgyzstan
        'LA', // Laos
        'LB', // Lebanon
        'MY', // Malaysia
        'BV', // Maldives
        'MN', // Mongolia
        'MM', // Myanmar (Burma)
        'NP', // Nepal
        'KP', // North Korea
        'OM', // Oman
        'PK', // Pakistan
        'PS', // Palestinian territories
        'PH', // Philippines
        'QA', // Qatar
        'RU', // Russia
        'SA', // Saudi Arabia
        'SG', // Singapore
        'LK', // Sri Lanka
        'KR', // South Korea
        'SY', // Syria
        'TW', // Taiwan
        'TJ', // Tajikistan
        'TH', // Thailand
        'TR', // Turkey
        'TM', // Turkmenistan
        'AE', // United Arab Emirates
        'UZ', // Uzbekistan
        'VN', // Vietnam
        'YE' // Yemen
    ]
};

pcuregions.caribbean = {
    countries: [
        // source is http://www.infoplease.com/ipa/A0877690.html
        'AI', // Anguilla
        'AG', // Antigua and Barbuda
        'BS', // Bahamas
        'BB', // Barbados
        'BM', // Bermuda
        'VG', // British Virgin Islands
        'KY', // Cayman Islands
        'DO', // Dominican Republic
        'DM', // Dominica
        'GD', // Grenada
        'JM', // Jamaica
        'MS', // Montserrat
        'KN', // St. Kitts & Nevis
        'LC', // St. Lucia
        'VC', // St. Vincent & the Grenadines
        'TT', // Trinidad & Tobago
        'TC' // Turks & Caicos
    ]
};

pcuregions.pacificIslands = {
    countries: [
        // source is http://data.worldbank.org/region/PSS
        'FJ', // Fiji
        'KI', // Kiribati
        'MH', // Marshall Islands
        'FM', // Micronesia, Fed. Sts.
        'PW', // Palau
        'WS', // Samoa
        'SB', // Solomon Islands
        'TO', // Tonga
        'TV', // Tuvalu
        'VU' // Vanuatu
    ]
};


