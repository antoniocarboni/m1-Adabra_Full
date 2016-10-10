<?php
class Adabra_Feed_Helper_Filesystem extends Mage_Core_Helper_Abstract
{
    protected $_locks = array();

    const PERMS_DIR = 0750;
    const PERMS_LOCK_FILE = 0600;

    /**
     * Get IO path
     *
     * @return string
     * @throws Exception
     */
    public function getExportPath()
    {
        $fileIo = new Varien_Io_File();

        $res = Mage::getBaseDir('var') . DS . 'adabra';
        $fileIo->checkAndCreateFolder($res, self::PERMS_DIR);

        return $res;
    }

    /**
     * Acquire non blocking lock
     *
     * @param $lockName
     * @return bool
     */
    public function acquireLock($lockName)
    {
        $lockFile = Mage::getBaseDir('tmp') . DS . $lockName . '.lck';

        // @codingStandardsIgnoreStart
        // Varien_Io_File only supports blocking locks
        $this->_locks[$lockName] = fopen($lockFile, 'w');
        chmod($lockFile, self::PERMS_LOCK_FILE);
        return flock($this->_locks[$lockName], LOCK_EX | LOCK_NB);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Release lock
     *
     * @param $lockName
     */
    public function releaseLock($lockName)
    {
        if (isset($this->_locks[$lockName])) {
            flock($this->_locks[$lockName], LOCK_UN);
            fclose($this->_locks[$lockName]);

            unset($this->_locks[$lockName]);
        }
    }
}
