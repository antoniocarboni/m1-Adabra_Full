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

        $compress = $helper->getCompress();
        $fileName = $feedInstance->getExportFile(false, $compress);
        $this->getResponse()->setHeader('Content-Type', $compress ? 'application/x-gzip' : 'text/csv');
        $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->getResponse()->setBody($feedInstance->getFeedContent($compress));
    }
}
