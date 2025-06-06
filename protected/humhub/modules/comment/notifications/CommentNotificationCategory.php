<?php



namespace humhub\modules\comment\notifications;

use Yii;
use humhub\modules\notification\components\NotificationCategory;

/**
 * CommentNotificationCategory
 *
 * @author buddha
 */
class CommentNotificationCategory extends NotificationCategory
{
    /**
     * @inheritdoc
     */
    public $id = "comments";

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('CommentModule.notification', 'Receive Notifications when someone comments on my own or a following post.');
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('CommentModule.notification', 'Comments');
    }

}
