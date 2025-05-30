<?php



/**
 * @noinspection PhpIllegalPsrClassPathInspection
 */

namespace tests\codeception\_support;

use yii\log\Logger as YiiLogger;

class Logger extends \Codeception\Lib\Connector\Yii2\Logger
{
    public ?YiiLogger $proxy = null;

    public function log($message, $level, $category = 'application')
    {
        YiiLogger::log($message, $level, $category);

        if ($this->proxy) {
            $this->proxy->log($message, $level, $category);
        }
    }
}
