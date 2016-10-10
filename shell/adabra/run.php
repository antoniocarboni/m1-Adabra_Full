<?php
// @codingStandardsIgnoreStart
require_once dirname(dirname(__FILE__)).'/abstract.php';
// @codingStandardsIgnoreEnd

class Adabra_Shell_Run extends Mage_Shell_Abstract
{
    public function run()
    {
        Mage::getSingleton('adabra_feed/feed')->exportNext();
    }
}

//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

$shell = new Adabra_Shell_Run();
$shell->run();
