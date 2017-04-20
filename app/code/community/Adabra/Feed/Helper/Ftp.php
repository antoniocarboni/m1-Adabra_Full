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

class Adabra_Feed_Helper_Ftp extends Mage_Core_Helper_Abstract
{
    /**
     * FTP upload
     * @param $localFileName
     * @param $remoteFileName
     * @throws Varien_Io_Exception
     */
    public function uploadFile($localFileName, $remoteFileName)
    {
        $helper = Mage::helper('adabra_feed');
        $ftp = new Varien_Io_Ftp();

        $transferMode = (strpos($remoteFileName, '.gz') === false) ? FTP_ASCII : FTP_BINARY;

        $ftp->open(array(
            'host' => $helper->getFtpHost(),
            'port' => $helper->getFtpPort(),
            'user' => $helper->getFtpUser(),
            'password' => $helper->getFtpPass(),
            'ssl' => $helper->getFtpSsl(),
            'passive' => $helper->getFtpPassive(),
            'path' => $helper->getFtpPath(),
            'file_mode' => $transferMode,
        ));

        $ftp->write($remoteFileName, $localFileName, $transferMode);
        $ftp->close();
    }
}
