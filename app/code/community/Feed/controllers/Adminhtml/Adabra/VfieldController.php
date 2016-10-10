<?php
class Adabra_Feed_Adminhtml_Adabra_VfieldController extends Mage_Adminhtml_Controller_Action
{
    protected function _prapareAction()
    {
        if (Mage::registry('adabra_vfield_data') && Mage::registry('adabra_vfield_data')->getId()) {
            return $this;
        }

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('adabra_feed/vfield');

        if ($id) {
            $model->load($id);
        }

        Mage::register('adabra_vfield_data', $model);
        return $this;
    }

    /**
     * Get current virtual field
     * @return Adabra_Feed_Model_Source_Vfield
     */
    protected function _getVfield()
    {
        return Mage::registry('adabra_vfield_data');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/adabra/vfield');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('adabra_feed/adminhtml_vfield'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_prapareAction();

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('adabra_feed/adminhtml_vfield_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $this->_prapareAction();

        $model = $this->_getVfield();
        $vfieldId = $model->getId();

        if ($this->getRequest()->getPost()) {
            try {
                if (!$vfieldId) {
                    Mage::throwException('Unknown virtual field identifier');
                    return;
                }

                $model
                    ->setMode($this->getRequest()->getPost('mode'))
                    ->setValue($this->getRequest()->getPost('value'))
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adabra_feed')->__('Virtual field was successfully saved')
                );

                $this->_redirect('*/*/edit', array('id' => $vfieldId));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $vfieldId));
                return;
            }
        }

        $this->_redirect('*/*/index');
    }
}
