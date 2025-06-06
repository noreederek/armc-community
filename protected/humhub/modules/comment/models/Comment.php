<?php


namespace humhub\modules\comment\models;

use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\comment\activities\NewComment;
use humhub\modules\comment\live\NewComment as NewCommentLive;
use humhub\modules\comment\Module;
use humhub\modules\comment\notifications\NewComment as NewCommentNotification;
use humhub\modules\comment\widgets\ShowMore;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentAddonActiveRecord;
use humhub\modules\content\services\ContentSearchService;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property string $message
 * @property int $object_id
 * @property string $object_model
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 * @property-read string $url @since 1.10.2
 *
 * @since 0.5
 */
class Comment extends ContentAddonActiveRecord
{
    public const CACHE_KEY_COUNT = 'commentCount_%s_%s';
    public const CACHE_KEY_LIMITED = 'commentsLimited_%s_%s';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['message'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => PolymorphicRelation::class,
                'mustBeInstanceOf' => [
                    ActiveRecord::class,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $this->flushCache();

        // Delete sub comment (replies)
        if ($this->object_model !== static::class) {
            foreach (static::findAll(['object_model' => static::class, 'object_id' => $this->id]) as $comment) {
                $comment->delete();
            }
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $this->updateContentSearch();
        parent::afterDelete();
    }

    /**
     * Flush comments cache
     */
    public function flushCache()
    {
        static::flushCommentCache($this->object_model, $this->object_id);
    }

    public static function flushCommentCache($model, $id)
    {
        Yii::$app->cache->delete(sprintf(static::CACHE_KEY_COUNT, $model, $id));
        Yii::$app->cache->delete(sprintf(static::CACHE_KEY_LIMITED, $model, $id));
    }

    /**
     * After Saving of comments, fire an activity
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->flushCache();

        if ($insert) {
            NewComment::instance()->about($this)->create();
        }

        // Handle mentioned users
        // Execute before NewCommentNotification to avoid double notification when mentioned.
        $processResult = RichText::postProcess($this->message, $this, 'message');
        $mentionedUsers = (isset($processResult['mentioning'])) ? $processResult['mentioning'] : [];

        if ($insert) {
            $followerQuery = $this->getCommentedRecord()->getFollowersWithNotificationQuery();

            // Remove mentioned users from followers query to avoid double notification
            if (count($mentionedUsers) !== 0) {
                $followerQuery->andWhere(['NOT IN', 'user.id', array_map(function (User $user) {
                    return $user->id;
                }, $mentionedUsers)]);
            }

            // Update updated_at etc..
            $this->refresh();

            NewCommentNotification::instance()->from($this->user)->about($this)->sendBulk($followerQuery);

            if ($this->content->container) {
                Yii::$app->live->send(new NewCommentLive([
                    'contentContainerId' => $this->content->container->id,
                    'visibility' => $this->content->visibility,
                    'contentId' => $this->content->id,
                    'commentId' => $this->id,
                ]));
            }
        }

        $this->updateContentSearch();

        parent::afterSave($insert, $changedAttributes);
    }


    /**
     * Force search update of underlying content object.
     * (This has also indexed the comments.)
     */
    protected function updateContentSearch()
    {
        if ($this->content) {
            (new ContentSearchService($this->content))->update();
        }
    }

    /**
     * Returns the commented record e.g. a Post
     *
     * @return ContentActiveRecord
     */
    public function getCommentedRecord()
    {
        return $this->content->getPolymorphicRelation();
    }

    /**
     * Returns a limited amount of comments
     *
     * @param string $model
     * @param int $id
     * @param int|null $limit when null the default limit will used
     * @param int|null $currentCommentId ID of the current Comment which should be visible on the limited result
     *
     * @return Comment[] the comments
     */
    public static function GetCommentsLimited($model, $id, $limit = null, $currentCommentId = null)
    {
        if ($limit === null) {
            /** @var Module $module */
            $module = Yii::$app->getModule('comment');
            $limit = $module->commentsPreviewMax;
        }

        if ($model === self::class && $currentCommentId == $id) {
            // No need to find current comment in sub-comments when parent comment is the current
            $currentCommentId = null;
        }
        $currentCommentId = intval($currentCommentId);
        $useCaching = empty($currentCommentId);// No need to cache comments for deep single comment view

        $cacheID = sprintf(static::CACHE_KEY_LIMITED, $model, $id);
        $comments = $useCaching ? Yii::$app->cache->get($cacheID) : [];
        if (!is_array($comments)) {
            $comments = [];
        }

        if (!isset($comments[$limit]) || !is_array($comments[$limit])) {
            $objectCondition = ['object_model' => $model, 'object_id' => $id];
            $query = Comment::find();
            if ($currentCommentId && Comment::findOne(['id' => $currentCommentId])) {
                // Get the current and one previous comment
                $nearCommentIds = Comment::find()
                    ->select('id')
                    ->where($objectCondition)
                    ->andWhere(['<=', 'id', $currentCommentId])
                    ->orderBy('created_at DESC')
                    ->limit($limit)
                    ->column();
                // Get 1 newer comment after the current comment
                $newerCommentIds = Comment::find()
                    ->select('id')
                    ->where($objectCondition)
                    ->andWhere(['>', 'id', $currentCommentId])
                    ->orderBy('created_at ASC')
                    ->limit(1)
                    ->column();
                $nearCommentIds = array_merge($nearCommentIds, $newerCommentIds);
                $query->where(['IN', 'id', $nearCommentIds]);
            } else {
                $query->where($objectCondition);
                $query->limit($limit);
            }
            $query->orderBy('created_at DESC, id dESC');
            $comments[$limit] = array_reverse($query->all());

            if ($useCaching) {
                Yii::$app->cache->set($cacheID, $comments, Yii::$app->settings->get('cacheExpireTime'));
            }
        }

        return $comments[$limit];
    }

    /**
     * Count number comments for this target object
     *
     * @param $model
     * @param $id
     *
     * @return int|mixed|string
     */
    public static function GetCommentCount($model, $id)
    {
        $cacheID = sprintf(static::CACHE_KEY_COUNT, $model, $id);
        $commentCount = Yii::$app->cache->get($cacheID);

        if ($commentCount === false) {
            $commentCount = Comment::find()->where(['object_model' => $model, 'object_id' => $id])->count();
            Yii::$app->cache->set($cacheID, $commentCount, Yii::$app->settings->get('cacheExpireTime'));
        }

        return $commentCount;
    }

    /**
     * Find more comments before or after a requested comment
     *
     * @param int|null $commentId ID of the latest comment from previous query
     * @param string|null $type
     * @param int|null $pageSize
     * @param Comment|ContentActiveRecord
     * @return Comment[]
     */
    public static function getMoreComments($object, ?int $commentId = null, ?string $type = null, ?int $pageSize = null): array
    {
        if (!$pageSize) {
            /** @var Module $module */
            $module = Yii::$app->getModule('comment');
            $pageSize = $module->commentsBlockLoadSize;
        }

        $query = Comment::find()
            ->where(['object_model' => get_class($object), 'object_id' => $object->getPrimaryKey()])
            ->limit($pageSize);

        if ($type === ShowMore::TYPE_NEXT) {
            $query->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC]);
            if ($commentId) {
                $query->andWhere(['>', 'id', $commentId]);
            }
            $comments = $query->all();
        } else {
            $query->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
            if ($commentId) {
                $query->andWhere(['<', 'id', $commentId]);
            }
            $comments = array_reverse($query->all());
        }

        return $comments;
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('CommentModule.base', 'comment');
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->message;
    }

    public function canDelete($userId = ''): bool
    {

        if ($userId == '') {
            $userId = Yii::$app->user->id;
        }

        if ($this->created_by == $userId) {
            return true;
        }

        if (Yii::$app->user->isAdmin()) {
            return true;
        }

        if ($this->content->container instanceof Space && $this->content->container->isAdmin($userId)) {
            return true;
        }

        return false;
    }

    /**
     * TODO: Unify with Content::isUpdated() see https://github.com/humhub/humhub/pull/4380
     * @returns bool true if this comment has been updated, otherwise false
     * @since 1.7
     */
    public function isUpdated()
    {
        return $this->created_at !== $this->updated_at && !empty($this->updated_at) && is_string($this->updated_at);
    }

    /**
     * Checks if given content object is a subcomment
     *
     * @param $object
     * @return bool
     * @since 1.8
     */
    public static function isSubComment($object)
    {
        return $object instanceof Comment && $object->object_model === Comment::class;
    }

    /**
     * Get comment permalink URL
     *
     * @param bool|string $scheme the URI scheme to use in the generated URL
     * @return string
     * @since 1.10.2
     */
    public function getUrl($scheme = true): string
    {
        if ($this->isNewRecord) {
            return $this->content->getUrl();
        }

        return Url::to(['/comment/perma', 'id' => $this->id], $scheme);
    }
}
