<?php
class Plugincompany_Productshippingultimate_Block_Preset_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $_preset;

    public function __construct()
    {
        parent::__construct();

        $this->_preset = Mage::registry('current_preset');

        $this->setId('preset_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getPreset()
    {
        return Mage::registry('current_preset');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in preset flag
        if ($column->getId() == 'in_preset') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->getPreset()->getId()) {
            $this->setDefaultFilter(array('in_preset'=>1));
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('weight')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('pcu_preset_id')
            ->addExpressionAttributeToSelect('pcu_preset_idd','COALESCE({{pcu_preset_id}},0)',array('pcu_preset_id'=>'pcu_preset_id'))
        ;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_preset', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_preset',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));


        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ));

        $presets = Mage::getResourceModel('plugincompany_productshippingultimate/preset_collection')->toOptionHash();

        $this->addColumn('current_preset',
            array(
                'header'=> Mage::helper('catalog')->__('Current Preset'),
                'width' => '100px',
                'index' => 'pcu_preset_idd',
                'type'  => 'options',
                'options' => $presets,
            ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');

        $post = $this->getRequest()->getPost();
        if (empty($post)) {
            $products = $this->_getOriginalProducts();
        }

        return $products;
    }


    protected function _getOriginalProducts()
    {
        $ids = Mage::registry('current_preset_products');
        if ($ids) {
            return $ids;
        }
        return array();
    }

}

