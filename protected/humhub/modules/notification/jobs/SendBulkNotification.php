<?php



namespace humhub\modules\notification\jobs;

use humhub\modules\queue\LongRunningActiveJob;
use humhub\modules\user\components\ActiveQueryUser;
use Yii;

/**
 * Description of SendNotification
 *
 * @author buddha
 * @since 1.2
 */
class SendBulkNotification extends LongRunningActiveJob
{
    /**
     * @var array Basenotification data as array.
     */
    public $notification;

    /**
     * @var ActiveQueryUser the query to determine which users should receive this notification
     */
    public $query;

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->notification->sendBulk($this->notification, $this->query);
    }
}
