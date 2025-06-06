<?php



namespace humhub\modules\user\notifications;

use Yii;
use humhub\modules\notification\components\NotificationCategory;

/**
 * Description of FollowingNotificationCategory
 *
 * @author buddha
 */
class FollowedNotificationCategory extends NotificationCategory
{
    /**
     * @inheritdoc
     */
    public $id = 'followed';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('UserModule.notification', 'Following');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('UserModule.notification', 'Receive Notifications when someone is following you.');
    }

}
