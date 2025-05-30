<?php


namespace humhub\modules\user\behaviors;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;
use humhub\modules\user\components\ActiveQueryUser;
use humhub\modules\user\models\Follow;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveQuery;

/**
 * HFollowableBehavior adds following methods to HActiveRecords
 *
 * @author Lucas Bartholemy <lucas.bartholemy@humhub.com>
 * @package humhub.modules_core.user.behaviors
 * @since 0.5
 */
class Followable extends Behavior
{
    /**
     * @inheritdoc
     * @var ContentContainerActiveRecord
     */
    public $owner;

    private $_followerCache = [];

    /**
     * Return the follow record based on the owner record and the given user id
     *
     * @param int $userId
     * @return Follow
     */
    public function getFollowRecord($userId)
    {
        $userId = ($userId instanceof User) ? $userId->id : $userId;
        return Yii::$app->runtimeCache->getOrSet(__METHOD__ . $this->owner->getPrimaryKey() . '-' . $userId, function () use ($userId) {
            return Follow::find()
                ->where([
                    'object_model' => get_class($this->owner),
                    'object_id' => $this->owner->getPrimaryKey(),
                    'user_id' => $userId,
                ])->one();
        });
    }

    /**
     * Follows the owner object
     *
     * @param int $userId
     * @param bool $withNotifications (since 1.2) sets the send_notifications setting of the membership default true
     * @return bool
     */
    public function follow($userId = null, $withNotifications = true)
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (!$userId || $userId == "") {
            $userId = Yii::$app->user->id;
        }

        // User cannot follow himself
        if ($this->owner instanceof User && $this->owner->id == $userId) {
            return false;
        } elseif ($this->owner instanceof Space && $this->owner->isMember($userId)) {
            return false;
        }

        $follow = $this->getFollowRecord($userId);
        if ($follow === null) {
            $follow = new Follow(['user_id' => $userId]);
            $follow->setPolyMorphicRelation($this->owner);
        }

        $follow->send_notifications = $withNotifications;

        if (!$follow->save()) {
            return false;
        }

        return true;
    }

    /**
     * Unfollows the owner object
     *
     * @param int $userId
     * @return bool
     */
    public function unfollow($userId = null)
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (!$userId || $userId == "") {
            $userId = Yii::$app->user->id;
        }

        $record = $this->getFollowRecord($userId);
        if ($record !== null) {
            if ($record->delete()) {
                return true;
            }
        } else {
            // Not follow this object
            return false;
        }

        return false;
    }

    /**
     * Checks if the given user follows this owner record.
     *
     * Note that the followers for this owner will be cached.
     *
     * @param int $userId
     * @param bool $withNotifications if true, only return true when also notifications enabled
     * @return bool Is object followed by user
     */
    public function isFollowedByUser($userId = null, $withNotifications = false)
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (!$userId || $userId == "") {
            $userId = Yii::$app->user->id;
        }

        if (!isset($this->_followerCache[$userId])) {
            $this->_followerCache[$userId] = $this->getFollowRecord($userId);
        }

        $record = $this->_followerCache[$userId];

        if ($record) {
            if ($withNotifications && $record->send_notifications == 1) {
                return true;
            } elseif (!$withNotifications) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a query of users which are followers of this object.
     *
     * @return ActiveQueryUser
     * @since 1.10
     */
    public function getFollowersQuery()
    {
        return User::find()
            ->leftJoin('user_follow', 'user.id = user_follow.user_id AND user_follow.object_id=:object_id AND user_follow.object_model = :object_model', [
                ':object_model' => get_class($this->owner),
                ':object_id' => $this->owner->getPrimaryKey(),
            ])
            ->where('user_follow.user_id IS NOT null')
            ->active()
            ->visible();
    }

    /**
     * Get a query of users which are followers with enabled notifications of this object.
     *
     * @return ActiveQueryUser
     * @since 1.10
     */
    public function getFollowersWithNotificationQuery()
    {
        return $this->getFollowersQuery()
            ->andWhere('user_follow.send_notifications=1');
    }


    /**
     * Get a query of objects which the owner object follows
     *
     * @param $query ActiveQuery e.g. `$user->getFollowingQuery(Content::find())`
     * @return ActiveQuery
     * @since 1.10
     */
    public function getFollowingQuery($query)
    {
        return $query
            ->leftJoin(
                'user_follow',
                'user.id=user_follow.object_id AND user_follow.object_model=:object_model',
                ['object_model' => get_class($this->owner)],
            )
            ->andWhere(['user_follow.user_id' => $this->owner->id])
            ->active();
    }
}
