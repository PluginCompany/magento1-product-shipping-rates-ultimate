<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Shipping Rates Ultimate, released under the MIT License.
 * See the LICENSE file in the project root.
 */

class Plugincompany_Productshippingultimate_Adminhtml_Pcshippingultimatepreset_IndexController extends Mage_Adminhtml_Controller_Action {

    public function indexAction()
    {
        $this->_title($this->__('Product Shipping Rate Per Country Presets'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('plugincompany_productshippingultimate/preset_grid'));
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select Preset(s) first.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('plugincompany_productshippingultimate/preset')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('plugincompany_productshippingultimate')->__('An error occurred while mass deleting items. Please review log and try again.')
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function editAction()
    {
        $this->_title($this->__('Edit Shipping Preset'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('plugincompany_productshippingultimate/preset');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('plugincompany_productshippingultimate')->__('This Product Shipping Rate Per Country Preset no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('current_preset', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
        $this->_addContent(
            $this
                ->getLayout()
                ->createBlock('plugincompany_productshippingultimate/preset_edit'))
            ->_addLeft(
                $this
                    ->getLayout()
                    ->createBlock("plugincompany_productshippingultimate/preset_edit_tabs")
            );
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('plugincompany_productshippingultimate/preset');
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->_getSession()->addError(
                        Mage::helper('plugincompany_productshippingultimate')->__('This Product Shipping Rate Per Country Preset no longer exists.')
                    );
                    $this->_redirect('*/*/index');
                    return;
                }
            }

            // save model
            try {
                $model->addData($data);
                $this->_getSession()->setFormData($data);
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(
                    Mage::helper('plugincompany_productshippingultimate')->__('The Product Shipping Rate Per Country Preset has been saved.')
                );

                //save products

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('plugincompany_productshippingultimate')->__('Unable to save the Preset.'));
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('plugincompany_productshippingultimate/preset');
                $model->load($id);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('plugincompany_productshippingultimate')->__('Unable to find a Preset to delete.'));
                }
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('plugincompany_productshippingultimate')->__('The Preset has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/index');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('plugincompany_productshippingultimate')->__('An error occurred while deleting Preset data. Please review log and try again.')
                );
                Mage::logException($e);
            }
            // redirect to edit form
            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }
// display error message
        $this->_getSession()->addError(
            Mage::helper('plugincompany_productshippingultimate')->__('Unable to find a Preset to delete.')
        );
// go to grid
        $this->_redirect('*/*/index');
    }

    public function productGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('plugincompany_productshippingultimate/preset');
        if ($id) {
            $model->load($id);
        }
        Mage::register('current_preset', $model);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('plugincompany_productshippingultimate/preset_edit_tab_product')->toHtml()
        );
    }
}