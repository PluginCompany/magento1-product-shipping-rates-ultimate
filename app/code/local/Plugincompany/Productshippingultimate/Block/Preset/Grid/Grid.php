<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Productshippingultimate_Block_Preset_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        $this->setDefaultSort('preset_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('plugincompany_productshippingultimate/preset')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

       $this->addColumn('preset_id',
           array(
               'header'=> $this->__('ID'),
               'width' => '50px',
               'index' => 'preset_id'
           )
       );
        $this->addColumn('title',
            array(
                'header'=> $this->__('Title'),
                'index' => 'title'
            )
        );
        $this->addColumn('per_item',
            array(
                'header'=> $this->__('Product Rate Calculation'),
                'index' => 'per_item',
                'width' => '50px',
                'type'  => 'options',
                'options' => Mage::getModel('plugincompany_productshippingultimate/source_peritem')->toOptionArray(true)
            )
        );
        $this->addColumn('override',
            array(
                'header'=> $this->__('Total Shipping Costs Calculation'),
                'index' => 'override',
                'width' => '50px',
                'type'  => 'options',
                'options' => Mage::getModel('plugincompany_productshippingultimate/source_override')->toOptionArray(true)
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
       return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $modelPk = Mage::getModel('plugincompany_productshippingultimate/preset')->getResource()->getIdFieldName();
        $this->setMassactionIdField($modelPk);
        $this->getMassactionBlock()->setFormFieldName('ids');
        // $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> $this->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
        ));
        return $this;
    }
}
