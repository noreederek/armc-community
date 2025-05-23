<?php



namespace tests\codeception\_support;

use Codeception\Events;

class HumHubExtension extends \Codeception\Extension
{
    public static $events = [
        Events::MODULE_INIT  => 'moduleInit',
        #Events::STEP_BEFORE => 'beforeStep',
        #Events::TEST_FAIL => 'testFailed',
        #Events::RESULT_PRINT_AFTER => 'print',
    ];

    public function moduleInit($test)
    {
        $GLOBALS['env'] = $this->options['env'];
    }
}
