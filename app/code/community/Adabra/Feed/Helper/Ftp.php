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

        $ftp->open(array(
            'host' => $helper->getFtpHost(),
            'port' => $helper->getFtpPort(),
            'user' => $helper->getFtpUser(),
            'password' => $helper->getFtpPass(),
            'ssl' => $helper->getFtpSsl(),
            'passive' => $helper->getFtpPassive(),
            'path' => $helper->getFtpPath(),
            'file_mode' => FTP_ASCII,
        ));

        $transferMode = (strpos($remoteFileName, '.gz') === false) ? FTP_ASCII : FTP_BINARY;

        $ftp->write($remoteFileName, $localFileName, $transferMode);
        $ftp->close();
    }
}
