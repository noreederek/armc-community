<?php



namespace humhub\modules\live;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\friendship\FriendshipEvent;
use humhub\modules\space\MemberEvent;
use humhub\modules\user\events\FollowEvent;
use humhub\modules\user\models\User;
use Yii;
use yii\base\BaseObject;

/**
 * Events provides callbacks to handle events.
 *
 * @since 1.2
 * @author luke
 */
class Events extends BaseObject
{
    /**
     * On hourly cron job, add database cleanup task
     */
    public static function onHourlyCronRun()
    {
        Yii::$app->queue->push(new jobs\DatabaseCleanup());
    }

    /**
     * MemberEvent is called when a user left or joined a space
     * Used to clear the cache legitimate cache.
     */
    public static function onMemberEvent(MemberEvent $event)
    {
        Yii::$app->getModule('live')->refreshLegitimateContentContainerIds($event->user);
    }

    /**
     * FriendshipEvent is called when a friendship was created or removed
     * Used to clear the cache legitimate cache.
     */
    public static function onFriendshipEvent(FriendshipEvent $event)
    {
        Yii::$app->getModule('live')->refreshLegitimateContentContainerIds($event->user1);
        Yii::$app->getModule('live')->refreshLegitimateContentContainerIds($event->user2);
    }

    /**
     * FollowEvent is called when a following was created or removed
     * Used to clear the cache legitimate cache.
     */
    public static function onFollowEvent(FollowEvent $event)
    {
        if ($event->target instanceof ContentContainerActiveRecord && $event->user instanceof User) {
            Yii::$app->getModule('live')->refreshLegitimateContentContainerIds($event->user);
        }
    }

}
