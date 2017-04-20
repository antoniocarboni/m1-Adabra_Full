<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   Adabra
 * @package    Adabra_Feed
 * @copyright  Copyright (c) 2017 Skeeller srl / MageSpecialist (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Adabra_Feed_Adminhtml_Adabra_FeedController extends Mage_Adminhtml_Controller_Action
{
    protected function _prapareAction()
    {
        if (Mage::registry('adabra_feed_data') && Mage::registry('adabra_feed_data')->getId()) {
            return $this;
        }

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('adabra_feed/feed');

        if ($id) {
            $model->load($id);
        }

        Mage::register('adabra_feed_data', $model);
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/adabra/feed');
    }

    /**
     * Get current feed
     * @return Adabra_Feed_Model_Feed
     */
    protected function _getFeed()
    {
        return Mage::registry('adabra_feed_data');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('adabra_feed/adminhtml_feed'));
        $this->renderLayout();
    }

    public function rebuildAction()
    {
        Mage::getSingleton('adabra_feed/feed')->rebuildAll();
        Mage::getSingleton('adminhtml/session')->addSuccess('All feeds marked for rebuild');

        $this->_redirectReferer();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_prapareAction();

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('adabra_feed/adminhtml_feed_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $this->_prapareAction();

        $model = $this->_getFeed();
        $feedId = $model->getId();

        if ($this->getRequest()->getPost()) {
            try {
                if (!$feedId) {
                    $model->setUpdatedAt(new Zend_Db_Expr('NOW()'));
                }

                $model
                    ->setStoreId($this->getRequest()->getPost('store_id'))
                    ->setEnabled($this->getRequest()->getPost('enabled'))
                    ->setCurrency($this->getRequest()->getPost('currency'))
                    ->save();

                if (!$feedId) {
                    $feedId = $model->getId();
                }

                if ($feedId) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adabra_feed')->__('Feed was successfully saved')
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('adabra_feed')->__('One error occurred while trying to save')
                    );
                }

                $this->_redirect('*/*/edit', array('id' => $feedId));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $feedId));
                return;
            }
        }

        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        $this->_prapareAction();
        $model = $this->_getFeed();

        if ($model->getId()) {
            try {
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adabra_feed')->__('Feed was successfully deleted')
                );
                $this->_redirect('*/*/index');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }

        $this->_redirect('*/*/index');
    }
}
