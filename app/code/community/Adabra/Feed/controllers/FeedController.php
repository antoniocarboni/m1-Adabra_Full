<?php
class Adabra_Feed_FeedController extends Mage_Core_Controller_Front_Action
{
    public function getAction()
    {
        $helper = Mage::helper('adabra_feed');
        $http = Mage::helper('core/http');

        if (!$helper->isHttpEnabled()) {
            return $this->norouteAction();
        }

        list($user, $pass) = $http->authValidate();

        if (($user != $helper->getHttpAuthUser()) ||
            ($pass != $helper->getHttpAuthPassword())
        ) {
            $http->authFailed();
        }
        
        $code = $this->getRequest()->getParam('code');

        $feedInstance = Mage::getSingleton('adabra_feed/feed')->getFeedTypeInstanceByCode($code);
        if (!$feedInstance || !$feedInstance->getFeed()->getEnabled()) {
            return $this->norouteAction();
        }

        $fileName = $feedInstance->getExportFile(false, true);
        $this->getResponse()->setHeader('Content-Type', 'application/x-gzip');
        $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->getResponse()->setBody($feedInstance->getFeedContent());
    }
}
