<?php
abstract class Adabra_Feed_Model_Feed_Abstract
{
    protected $_type = null;
    protected $_exportName = null;
    protected $_idFieldName = null;
    protected $_scope = 'store';

    /** @var Varien_Data_Collection_Db */
    protected $_collection = null;

    /** @var Adabra_Feed_Model_Feed $feed */
    protected $_feed;

    protected $_lastId = 0;

    /**
     * Set export feed
     * @param Adabra_Feed_Model_Feed $feed
     * @return $this
     */
    public function setFeed(Adabra_Feed_Model_Feed $feed)
    {
        $this->_feed = $feed;
        return $this;
    }

    /**
     * Get current feed
     * @return Adabra_Feed_Model_Feed
     */
    public function getFeed()
    {
        return $this->_feed;
    }

    /**
     * Get feed store ID
     * @return int
     */
    public function getStoreId()
    {
        return $this->getFeed()->getStore()->getId();
    }

    /**
     * Get feed store
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getFeed()->getStore();
    }

    /**
     * Get export path
     * @return string
     */
    public function getExportPath()
    {
        return Mage::helper('adabra_feed/filesystem')->getExportPath();
    }

    /**
     * Get feed code
     * @return string
     */
    public function getCode()
    {
        return $this->getFeed()->getCode($this->_scope) . '_' . $this->_type;
    }

    /**
     * Get feed file name
     * @return string
     */
    public function getFeedFileName()
    {
        return $this->getFeed()->getCode($this->_scope) . '_' . $this->_exportName . '.csv';
    }

    /**
     * Get exported filename
     * @param $chunked
     * @param bool $compressed
     * @return string
     */
    public function getExportFile($chunked, $compressed = false)
    {
        $filename = $this->getFeedFileName() . ($compressed ? '.gz' : '');
        return $chunked ? $filename . "." . $this->_getLastId() : $filename;
    }

    /**
     * Get collection ID field Name
     * @return string
     */
    protected function _getIdFieldName()
    {
        if ($this->_idFieldName) {
            return $this->_idFieldName;
        }

        if ($this->_collection->getIdFieldName()) {
            return $this->_collection->getIdFieldName();
        }

        return 'entity_id';
    }

    /**
     * Get headers
     * @return array
     */
    abstract protected function _getHeaders();

    /**
     * Get feed row
     * @param Varien_Object $entity
     * @return array
     */
    abstract protected function _getFeedRow(Varien_Object $entity);

    /**
     * Prepare collection
     */
    abstract protected function _prepareCollection();

    /**
     * Reset collection
     */
    protected function _resetCollection()
    {
        $this->_collection = null;
    }

    /**
     * Get collection
     * @return Varien_Data_Collection
     */
    protected function _getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_prepareCollection();
            $this->_addPagination();
            $this->_addPositionFilter();
            $this->_addCollectionSort();
        }

        return $this->_collection;
    }

    /**
     * Adds pagination to collection
     */
    protected function _addPagination()
    {
        if (!$this->isBatchEnabled()) {
            return;
        }

        $this->_collection->getSelect()->limit($this->getBatchSize());
    }
    
    /**
     * Adds last id filter to collection
     */
    protected function _addPositionFilter()
    {
        if (!$this->isBatchEnabled()) {
            return;
        }

        $this->_collection->addFieldToFilter(
            $this->_getIdFieldName(),
            array(
                'gt' => $this->_getLastId(),
            )
        );
    }

    /**
     * Sorts collection by id
     */
    protected function _addCollectionSort()
    {
        $this->_collection->setOrder(
            $this->_getIdFieldName(),
            Varien_Data_Collection::SORT_ORDER_ASC
        );
    }

    /**
     * Return true when products batch mode is enabled
     * @return bool
     */
    protected function isBatchEnabled()
    {
        return Mage::helper('adabra_feed')->isBatchEnabled($this->_type);
    }

    /**
     * Get products batch size
     * @return int
     */
    protected function getBatchSize()
    {
        return Mage::helper('adabra_feed')->getBatchSize($this->_type);
    }

    /**
     * Export entity
     * @param Varien_Io_File $fileIo
     * @param Varien_Object $entity
     * @return int
     */
    protected function _export(Varien_Io_File $fileIo, Varien_Object $entity)
    {
        $rows = $this->_getFeedRow($entity);

        foreach ($rows as $row) {
            $fileIo->streamWriteCsv($row);
        }

        return count($rows);
    }

    /**
     * Conver to boolean
     * @param $val
     * @return string
     */
    protected function _toBoolean($val)
    {
        return $val ? 'true' : 'false';
    }

    /**
     * Convert value to currency
     * @param $val
     * @param $currencyConvert
     * @return string
     */
    protected function _toCurrency($val, $currencyConvert = false)
    {
        if ($currencyConvert) {
            $baseCurrency = $this->getStore()->getBaseCurrencyCode();
            $val = Mage::helper('directory')->currencyConvert($val, $baseCurrency, $this->getFeed()->getCurrency());
        }

        return number_format($val, 4, '.', '');
    }

    /**
     * Convert value to currency
     * @param $ts
     * @return string
     */
    protected function _toTimestamp($ts)
    {
        return
            Mage::getSingleton('core/date')->date('Y-m-d', $ts).'T'
            .Mage::getSingleton('core/date')->date('H:i:s', $ts);
    }

    /**
     * Convert value to currency
     * @param $ts
     * @return string
     */
    protected function _toTimestamp2($ts)
    {
        return
            Mage::getSingleton('core/date')->date('Y-m-d', $ts).' '
            .Mage::getSingleton('core/date')->date('H:i:s', $ts);
    }

    /**
     * Get last processed id
     * @return int
     */
    protected function _getLastId()
    {
        return $this->_lastId;
    }

    /**
     * Sets last processed id
     * @param $value
     */
    protected function _setLastId($value)
    {
        $this->_lastId = $value;
    }

    /**
     * Get progress file name
     * @return string
     */
    protected function _getPositionFileName()
    {
        return $this->getExportFile(false).'.idx';
    }

    /**
     * Loads last processed id from position file
     * @throws Exception
     */
    protected function _loadPosition()
    {
        $filePosition = new Varien_Io_File();

        $exportPath = $this->getExportPath();
        $filePosition->open(array('path' => $exportPath));

        if ($filePosition->fileExists($this->_getPositionFileName())) {
            $filePosition->streamOpen($this->_getPositionFileName(), 'r');
            $position = $filePosition->streamRead();
            $filePosition->streamClose();
        } else {
            $position = 0;
        }
        
        $this->_setLastId($position);
    }

    /**
     * Saves last processed id to position file
     * @throws Exception
     */
    protected function _savePosition()
    {
        if (!$this->isBatchEnabled()) {
            return;
        }

        $filePosition = new Varien_Io_File();
        $exportPath = $this->getExportPath();
        $filePosition->open(array('path' => $exportPath));
        $filePosition->streamOpen($this->_getPositionFileName(), 'w');
        $filePosition->streamWrite($this->_getLastId());
        $filePosition->streamClose();
    }

    /**
     * Get virtual rows
     * @return array
     */
    protected function _getVirtualRows()
    {
        return array();
    }

    /**
     * Assemble CSV files
     */
    protected function _assembleFiles()
    {
        $fileIo = new Varien_Io_File();

        $fileName = $this->getExportFile(false);

        $exportPath = $this->getExportPath();
        $fileIo->open(array('path' => $exportPath));

        $fileIo->streamOpen($fileName, 'w');
        $fileIo->streamLock(true);

        $fileIo->streamWriteCsv($this->_getHeaders());

        $virtualRows = $this->_getVirtualRows();
        foreach ($virtualRows as $virtualRow) {
            $fileIo->streamWriteCsv($virtualRow);
        }

        $chunkFiles = array();

        // Enumerate files
        $chunks = $fileIo->ls();
        foreach ($chunks as $chunk) {
            $chunkFile = $chunk['text'];
            if ((strpos($chunkFile, $fileName) !== false) &&
                preg_match('/\.(\d+)$/', $chunkFile, $matches)
            ) {
                $chunkFiles[intval($matches[1])] = $chunkFile;
            }
        }

        ksort($chunkFiles, SORT_NUMERIC);

        // Append files and remove
        foreach ($chunkFiles as $chunkFile) {
            $fileIo->streamWrite($fileIo->read($chunkFile));
            $fileIo->rm($chunkFile);
        }

        $fileIo->rm($this->_getPositionFileName());

        $fileIo->streamUnlock();
        $fileIo->streamClose();
    }

    /**
     * Get current feed build status
     * @return string
     */
    public function getBuildStatus()
    {
        return $this->getFeed()->getData('status_'.$this->_type);
    }

    /**
     * Get feed content
     * @return string
     */
    public function getFeedContent()
    {
        $fileIo = new Varien_Io_File();

        $fileName = $this->getExportFile(false, true);

        $exportPath = $this->getExportPath();
        $fileIo->open(array('path' => $exportPath));

        return $fileIo->read($fileName);
    }

    /**
     * Get feed URL
     * @return string
     */
    public function getUrl()
    {
        return Mage::getUrl('adabra_feed/feed/get', array('code' => $this->getCode()));
    }

    /**
     * Change current build status
     * @param $status
     * @return $this
     */
    public function changeBuildStatus($status)
    {
        if ($this->_scope == 'website') {
            $this->getFeed()->changeStatusForWebsite($this->_type, $status);
        } else {
            $this->getFeed()->changeStatus($this->_type, $status);
        }

        return $this;
    }

    /**
     * Compress feed file
     */
    protected function _gzipCompression()
    {
        $exportPath = $this->getExportPath();
        $plainFile = $exportPath . DS . $this->getExportFile(false, false);
        $compressedFile = $exportPath . DS . $this->getExportFile(false, true);

        // @codingStandardsIgnoreStart
        $fp = gzopen($compressedFile, 'w9');
        gzwrite($fp, file_get_contents($plainFile));
        gzclose($fp);
        // @codingStandardsIgnoreEnd

        // Remove plain file
        $fileIo = new Varien_Io_File();
        $fileIo->open(array('path' => $exportPath));
        $fileIo->rm($plainFile);
    }

    /**
     * Run export task
     */
    public function export()
    {
        if ($this->getBuildStatus() == Adabra_Feed_Model_Source_Status::READY) {
            return;
        }

        Mage::app()->setCurrentStore($this->getStore()->getCode());

        $this->_loadPosition();
        $collection = $this->_getCollection();

        $fileName = $this->getExportFile(true);
        $exportPath = $this->getExportPath();

        $fileIo = new Varien_Io_File();
        $fileIo->open(array('path' => $exportPath));
        $fileIo->streamOpen($fileName, 'w');
        $fileIo->streamLock(true);

        $this->changeBuildStatus(Adabra_Feed_Model_Source_Status::BUILDING);
        foreach ($collection as $entity) {
            $this->_export($fileIo, $entity);
            $this->_setLastId($entity->getId());
        }

        $this->_savePosition();

        $fileIo->streamUnlock();
        $fileIo->streamClose();

        // Check if collection is finished
        $this->_resetCollection();
        $collection = $this->_getCollection();
        if (!$collection->getSize() || !$this->isBatchEnabled()) {
            $this->_assembleFiles();
            $this->changeBuildStatus(Adabra_Feed_Model_Source_Status::READY);

            $compress = Mage::helper('adabra_feed')->getCompress();
            if ($compress) {
                $this->_gzipCompression();
            }

            if (Mage::helper('adabra_feed')->isFtpEnabled()) {
                Mage::helper('adabra_feed/ftp')->uploadFile(
                    $exportPath . DS . $this->getExportFile(false, $compress),
                    $this->getExportFile(false, $compress)
                );
            }
        }
    }
}
