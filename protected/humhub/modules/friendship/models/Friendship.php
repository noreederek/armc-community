<?php



namespace humhub\modules\friendship\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\friendship\FriendshipEvent;
use humhub\modules\friendship\notifications\RequestDeclined;
use humhub\modules\friendship\notifications\Request;
use humhub\modules\friendship\notifications\RequestApproved;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "user_friendship".
 *
 * @property int $id
 * @property int $user_id
 * @property int $friend_user_id
 * @property string $created_at
 *
 * @property User $friendUser
 * @property User $user
 */
class Friendship extends ActiveRecord
{
    /**
     * @event \humhub\modules\friendship\FriendshipEvent
     */
    public const EVENT_FRIENDSHIP_CREATED = 'friendshipCreated';

    /**
     * @event \humhub\modules\friendship\FriendshipEvent
     */
    public const EVENT_FRIENDSHIP_REMOVED = 'friendshipRemoved';

    /**
     * Friendship States
     */
    public const STATE_NONE = 0;
    public const STATE_FRIENDS = 1;
    public const STATE_REQUEST_RECEIVED = 2;
    public const STATE_REQUEST_SENT = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_friendship';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'friend_user_id'], 'required'],
            [['user_id', 'friend_user_id'], 'integer'],
            [['user_id', 'friend_user_id'], 'unique', 'targetAttribute' => ['user_id', 'friend_user_id'], 'message' => 'The combination of User ID and Friend User ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'friend_user_id' => 'Friend User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFriendUser()
    {
        return $this->hasOne(User::class, ['id' => 'friend_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // Check if this is an request (friend has no entry in table)
            $state = self::getStateForUser($this->user, $this->friendUser);

            if ($state === self::STATE_REQUEST_SENT) {
                // Send Request Notification
                Request::instance()->from($this->user)->about($this)->send($this->friendUser);
            } elseif ($state === self::STATE_FRIENDS) {
                // Remove request notification
                Request::instance()->from($this->friendUser)->delete($this->user);

                // User approved friends request notification
                RequestApproved::instance()->from($this->user)->about($this)->send($this->friendUser);

                $this->trigger(self::EVENT_FRIENDSHIP_CREATED, new FriendshipEvent([
                    'user1' => $this->user,
                    'user2' => $this->friendUser,
                ]));
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Returns the friendship state between to users
     *
     * @param User $user
     * @param User $friend
     *
     * @return int the request state see self::STATE_*
     */
    public static function getStateForUser($user, $friend)
    {
        $state = self::STATE_NONE;

        if (self::getSentRequestsQuery($user)->andWhere(['user.id' => $friend->id])->one() !== null) {
            $state = self::STATE_REQUEST_SENT;
        } elseif (self::getFriendsQuery($user)->andWhere(['user.id' => $friend->id])->one() !== null) {
            $state = self::STATE_FRIENDS;
        } elseif (self::getReceivedRequestsQuery($user)->andWhere(['user.id' => $friend->id])->one() !== null) {
            $state = self::STATE_REQUEST_RECEIVED;
        }

        return $state;
    }

    /**
     * Returns a query for friends of a user
     *
     * @param User $user
     * @return ActiveQuery
     */
    public static function getFriendsQuery(User $user)
    {
        $query = User::find();

        // Users which received a friend requests from given user
        $query->leftJoin('user_friendship recv', 'user.id=recv.friend_user_id AND recv.user_id=:userId', [':userId' => $user->id]);
        $query->andWhere(['IS NOT', 'recv.id', new Expression('NULL')]);

        // Users which send a friend request to given user
        $query->leftJoin('user_friendship snd', 'user.id=snd.user_id AND snd.friend_user_id=:userId', [':userId' => $user->id]);
        $query->andWhere(['IS NOT', 'snd.id', new Expression('NULL')]);

        return $query;
    }

    /**
     * Returns a query selecting container ids of users the given $user has a friendship relation.
     *
     * @param User $user
     * @return Query
     * @since 1.8
     */
    public static function getFriendshipContainerIdQuery(User $user)
    {
        return (new Query())
            ->select('ufr.contentcontainer_id AS id')
            ->distinct()
            ->from('user ufr')
            ->indexBy('id')
            ->innerJoin('user_friendship recv', 'ufr.id = recv.friend_user_id AND recv.user_id = :userId', [':userId' => $user->id])
            ->innerJoin('user_friendship snd', 'ufr.id = snd.user_id AND snd.friend_user_id = :userId', [':userId' => $user->id]);
    }

    /**
     * Returns a query for sent and not approved friend requests of an user
     *
     * @param User $user
     * @return ActiveQuery
     */
    public static function getSentRequestsQuery(User $user)
    {
        $query = User::find();

        // Users which received a friend requests from given user
        $query->leftJoin('user_friendship recv', 'user.id=recv.friend_user_id AND recv.user_id=:userId', [':userId' => $user->id]);
        $query->andWhere(['IS NOT', 'recv.id', new Expression('NULL')]);

        // Users which NOT send a friend request to given user
        $query->leftJoin('user_friendship snd', 'user.id=snd.user_id AND snd.friend_user_id=:userId', [':userId' => $user->id]);
        $query->andWhere(['IS', 'snd.id', new Expression('NULL')]);

        return $query;
    }

    /**
     * Returns a query for received and not responded friend requests of an user
     *
     * @param User $user
     * @return ActiveQuery
     */
    public static function getReceivedRequestsQuery($user)
    {
        $query = User::find();

        // Users which NOT received a friend requests from given user
        $query->leftJoin('user_friendship recv', 'user.id=recv.friend_user_id AND recv.user_id=:userId', [':userId' => $user->id]);
        $query->andWhere(['IS', 'recv.id', new Expression('NULL')]);

        // Users which send a friend request to given user
        $query->leftJoin('user_friendship snd', 'user.id=snd.user_id AND snd.friend_user_id=:userId', [':userId' => $user->id]);
        $query->andWhere(['IS NOT', 'snd.id', new Expression('NULL')]);

        return $query;
    }

    /**
     * Adds a friendship or sends a request
     *
     * @param User $user
     * @param User $friend
     * @return bool
     */
    public static function add($user, $friend)
    {
        $friendship = new Friendship();
        $friendship->user_id = $user->id;
        $friendship->friend_user_id = $friend->id;
        if ($friendship->save()) {
            $friend->follow($user, false);
            return true;
        }

        return false;
    }

    /**
     * Cancels a friendship or request to a friend
     *
     * @param User $user
     * @param User $friend
     */
    public static function cancel($user, $friend)
    {
        // Delete friends entry
        $myFriendship = Friendship::findOne(['user_id' => $user->id, 'friend_user_id' => $friend->id]);
        $friendsFriendship = Friendship::findOne(['user_id' => $friend->id, 'friend_user_id' => $user->id]);

        if ($friendsFriendship !== null) {
            $friendsFriendship->delete();
        }

        if ($myFriendship !== null) {
            $myFriendship->delete();
        } elseif ($friendsFriendship !== null) {
            // Is declined friendship request - send declined notification
            RequestDeclined::instance()->from($user)->send($friend);
        }

        if ($myFriendship !== null && $friendsFriendship !== null) {
            // Trigger event is friendship was mutual
            FriendshipEvent::trigger(Friendship::class, Friendship::EVENT_FRIENDSHIP_REMOVED, new FriendshipEvent([
                'user1' => $user, 'user2' => $friend,
            ]));
        }
    }

}
