<?php
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
