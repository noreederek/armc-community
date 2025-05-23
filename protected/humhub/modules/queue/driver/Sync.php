<?php



namespace humhub\modules\queue\driver;

use yii\queue\sync\Queue;

/**
 * Sync queue driver
 *
 * @since 1.2
 * @author Luke
 */
class Sync extends Queue
{
    /**
     * @inheritdoc
     */
    public $handle = true;

}
