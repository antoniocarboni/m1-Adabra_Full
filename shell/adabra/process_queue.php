<?php
// @codingStandardsIgnoreStart
require_once dirname(dirname(__FILE__)).'/abstract.php';
// @codingStandardsIgnoreEnd

class Adabra_Shell_ProcessQueue extends Mage_Shell_Abstract
{
    public function run()
    {
        /** @var Adabra_Realtime_Model_Queue $queue */
        $queue = Mage::getSingleton('adabra_realtime/queue');
        $queue->processQueue();
    }
}

//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

$shell = new Adabra_Shell_ProcessQueue();
$shell->run();