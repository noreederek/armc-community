<?php



namespace humhub\modules\admin\jobs;

use humhub\modules\queue\ActiveJob;
use humhub\modules\admin\models\Log;

/**
 * CleanupLog deletes older log records from log table
 *
 * @since 1.2
 * @author Luke
 */
class CleanupLog extends ActiveJob
{
    /**
     * @var int seconds before delete old log messages
     */
    public $cleanupInterval = 60 * 60 * 24 * 7;

    /**
     * @inheritdoc
     */
    public function run()
    {
        Log::deleteAll(['<', 'log_time', time() - $this->cleanupInterval]);
    }

}
