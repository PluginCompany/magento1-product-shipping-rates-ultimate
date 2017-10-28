<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */

/**
 * Form element class for country / rate input
 *
 * Class Varien_Data_Form_Element_Pcushipping
 */
class Varien_Data_Form_Element_Pcshippingultimate extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
		Mage::app()->getLayout()->getBlock('head')->addJs('plugincompany/jquerynoconflict.js');
		Mage::app()->getLayout()->getBlock('head')->addJs('plugincompany/productshippingultimate/rate_selector.js');
    }

    /**
     * @return mixed|string
     */
    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'disabled', 'readonly', 'maxlength', 'tabindex');
    }

    /**
     * Hidden field with actual values
     *
     * @return string
     */
    public function getElementHtml()
    {
        $value = $this->getSortedData(json_decode($this->getEscapedValue()));
        $html = '<input type=text style="display:none;width:450px;vertical-align:top;" class="pcu_shipping_per_country" id="'.$this->getHtmlId().'" name="'.$this->getName();
        $html .= '" value='.json_encode($value).' '.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        $html .= $this->getdatatable();
        $html .= $this->getAfterElementHtml();
        $html .= $this->getScripts();
        unset($value);
        return $html;
    }

    /**
     * The input widget
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<div class="entry-edit-head" style="width:430px;background:#83a9c4;margin-top:4px;"><h4>Add Shipping Rates per Country and Customer Group</h4></div>';
        $html .= '<div id="plugshipu" name="plugshipu" style="padding:4px;background:#d7e5ef;width:442px;height:260px;">';
        $html .= $this->getCustGroupDropDown();
        $html .= $this->getdropdown();
        $html .= '<div id="pcuregions"><p><b>Select a region:</b></p></div>';
        $html .= '<input id="pcuprice" style="margin-right:4px" class="validate-zero-or-greater" /><button style="margin-top:10px" type="button" onClick="addshippingvaluesUltimate();">Add</button>';
        $html .= '</div>';
        $html .= parent::getAfterElementHtml();
        return $html;
    }

    /**
     * Customer Group dropdown
     *
     * @return string
     */
    public function getCustGroupDropDown(){
        $html = "";
        $group = $this->getCustomerGroup();
        if (count($group)>0){
            $html .= '<div id="custgroup" style="float:left;margin-left:10px;width:430px;" ><label for="pcuCustGroup" style="width=100px;display:inline" ><b>Customer Group:</b></label>';
            $html .= '<select style="width: 314px; display: inline; margin-left: 15px; margin-top: 8px; margin-bottom: 8px;" name="pcuCustGroup" id="pcuCustGroup" onchange="pcGroupChange();" >'; // oninput onchange onselect
            $html .= '<option id="pcuzero" value="">-- Please Select --</option>';
            foreach ($group as $grp){
                $html .= "<option value=".$grp['group_id']." >".$grp['group_code']."</option>";
            }
            $html .= '</select></br></div>';
        }
        unset($group);
        return $html;
    }

    /**
     * Customer Group Array
     *
     * @return array|bool|string
     */
    public function getCustomerGroup(){
        $group = Mage::getModel('customer/group')->getCollection();
        $groupArray = array(array('group_id' => "-1", 'group_code' => "All Groups"));

        foreach ($group as $data){
            array_push($groupArray, array('group_id' => $data->getCustomerGroupId(),
            'group_code' => $data->getCustomerGroupCode()));
        }
        unset($group);
        return $groupArray;
    }
    
    /**
     * Countries dropdown
     *
     * @return string
     */
    public function getdropdown(){
        $_countries = Mage::getResourceModel('directory/country_collection')
                    ->loadByStore()                                  
                    ->toOptionArray(false);	
        if (count($_countries) > 0){
            $html = '<select multiple style="width:200px;float:left;margin:4px;height:212px;" name="country" id="pcucountry" >
            <option id="pcuzero" value="0">-- Please Select --</option>
            <option value="-">Default rate</option>';
            foreach($_countries as $_country){
                $html .= '<option value="'.$_country['value'].'">'.$_country['label'].'</option>';
            }
            $html .= '</select>';
        }
        unset($_countries);
        return $html;
    }

    /**
     * Unserialize string from input
     *
     * @return array|bool|string
     */
    public function getvaluearray(){
        $value = $this->getSortedData(json_decode($this->getEscapedValue()));
        return $value;
    }
    
    /**
     * Sort input data, based on Customer Group, Country Name
     * @param $data array
     * @return array|bool|string
     */
    public function getSortedData($data){
        $arr = Array();
        if(!$data){
            return $arr;
        }
        foreach ($data as $val){
            array_push($arr, Array($val[0], Mage::app()->getLocale()->getCountryTranslation($val[1]), $val[1], $val[2]));
        }
        usort($arr, function($a, $b){
            if ($a[0]<$b[0]) {$x=-1;} else {$x=0;}
            if ($a[0]>$b[0]) {$x=1;}
            if ($a[1]<$b[1]) {$y=-2;} else {$y=0;}
            if ($a[1]>$b[1]) {$y=2;}
            return $x+$y; 
        });
        foreach ($arr as $key=>$val){
            array_splice($arr[$key], 1, 1);
        }
        return $arr;
    }
    
    /**
     * Table with current values
     *
     * @return string
     */
    public function getdatatable(){
        $html = "<span class='grid'><table id='pcushippingtable' cellspacing='0' style='width:450px' class='data border'>";
        $html .= "<thead><tr class='headings'><th style='width:20px' >Code</th><th >Country</th><th style='width:100px'>Customer Group</th><th style='width:40px;text-align:right;'>Shipping Rate</th><th style='width:30px'></th></tr></thead><tbody>";
        $value = $this->getvaluearray();
        foreach ($value as $v){
            if ($v[0] === false || $v[0] === null || $v[0] === '') {
                continue;
            }
            if($v[1] === '-'){$standard = 'Default rate';}else{$standard = '';}
            $html .= "<tr class='pcushipping ".$v[1].'-'.$v[0]."'><td style='text-align:center' >".$v[1]."</td><td >".Mage::app()->getLocale()->getCountryTranslation($v[1]).$standard."</td><td>".$this->getGroupName($v[0])."</td><td style='text-align:right'>".$v[2]."</td><td><button title='Delete row' type='button' class='scalable delete icon-btn delete-product-option pcudelete' onclick='pcudeletitem(this)'><span><span><span>Delete</span></span></span></button></td></tr>";
        }
        $html .= "</tbody></table></span>";
        unset($value);
        return $html;
    }
    
    public function getGroupName($groupId){
        $group = $this->getCustomerGroup();
        foreach ($group as $data){
            if ($data["group_id"]===$groupId) {return $data["group_code"];}
        }
        unset($group);
        return "";
    }


    public function getEscapedValue($index=null)
    {
        $value = $this->getValue($index);

        if ($product = Mage::registry('current_product')) {
            if ($pId = $product->getPcuPresetId()) {
                $value = Mage::getModel('plugincompany_productshippingultimate/preset')->load($pId)->getShippingPerCountry();
            }
        }

        if ($filter = $this->getValueFilter()) {
            $value = $filter->filter($value);
        }

        if ($value == '0' || $value == '0;' || $value == ';') {
            return '';
        }

        return $value;
    }

    public function getScripts()
    {
        if ($product = Mage::registry('current_product')) {
            if ($pId = $product->getPcuPresetId()) {
                $preset = Mage::getModel('plugincompany_productshippingultimate/preset')->load($pId);
                $perItem = $preset->getPerItem();
                $override = $preset->getOverride();
                $script = "
                    <script type='text/javascript'>
                    jQuery(document).ready(function(){
                        jQuery('#pcu_per_item').append('<option value=\'3\'>Once per preset</option>');
                        jQuery('#pcu_per_item').val($perItem);
                        jQuery('#pcu_override').val($override);
                    })
                    </script>
                ";
                return $script;
            }
        }
    }
}


