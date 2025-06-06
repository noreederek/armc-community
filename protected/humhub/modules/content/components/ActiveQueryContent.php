<?php



namespace humhub\modules\content\components;

use humhub\modules\content\models\Content;
use humhub\modules\content\models\ContentTag;
use humhub\modules\content\models\ContentTagRelation;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\helpers\AuthHelper;
use humhub\modules\user\models\User;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

/**
 * ActiveQueryContent is an enhanced ActiveQuery with additional selectors for especially content.
 *
 * @inheritdoc
 *
 * @author luke
 */
class ActiveQueryContent extends ActiveQuery
{
    /**
     * Own content scope for userRelated
     * @see ActiveQueryContent::userRelated
     */
    public const USER_RELATED_SCOPE_OWN = 1;
    public const USER_RELATED_SCOPE_SPACES = 2;
    public const USER_RELATED_SCOPE_FOLLOWED_SPACES = 3;
    public const USER_RELATED_SCOPE_FOLLOWED_USERS = 4;
    public const USER_RELATED_SCOPE_OWN_PROFILE = 5;

    /**
     * State filter that is used for queries. By default, only Published content is returned.
     *
     * Example to include drafts:
     * ```
     * $query = Post::find();
     * $query->stateFilterCondition[] = ['content.state' => Content::STATE_DRAFT];
     * $posts = $query->readable()->all();
     * ```
     *
     * @since 1.14
     * @var array
     */
    public $stateFilterCondition = ['OR', ['content.state' => Content::STATE_PUBLISHED]];

    /**
     * Only returns user readable records
     *
     * @param User $user
     * @return ActiveQueryContent
     * @throws Throwable
     */
    public function readable($user = null)
    {
        if ($user === null && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
        }

        $this->andWhere($this->stateFilterCondition);

        $this->joinWith(['content', 'content.contentContainer', 'content.createdBy']);
        $this->leftJoin('space', 'contentcontainer.pk=space.id AND contentcontainer.class=:spaceClass', [':spaceClass' => Space::class]);
        $this->leftJoin('user cuser', 'contentcontainer.pk=cuser.id AND contentcontainer.class=:userClass', [':userClass' => User::class]);

        if (!Yii::$app->getModule('stream')->showDeactivatedUserContent) {
            $this->andWhere(['user.status' => User::STATUS_ENABLED]);
        }

        if ($user !== null) {
            $this->leftJoin('space_membership', 'contentcontainer.pk=space_membership.space_id AND contentcontainer.class=:spaceClass AND space_membership.user_id=:userId', [':userId' => $user->id, ':spaceClass' => Space::class]);

            if ($user->canManageAllContent()) {
                // Don't restrict if user can view all content:
                $conditionSpaceMembershipRestriction = '';
                $conditionUserPrivateRestriction = '';
            } else {
                // User must be a space's member OR Space and Content are public
                $conditionSpaceMembershipRestriction = ' AND ( space_membership.status=3 OR (content.visibility=1 AND space.visibility != 0) )';
                // User can view only content of own profile
                $conditionUserPrivateRestriction = ' AND content.contentcontainer_id=' . $user->contentcontainer_id;
            }

            // Build Access Check based on Space Content Container
            $conditionSpace = 'space.id IS NOT NULL' . $conditionSpaceMembershipRestriction;

            // Build Access Check based on User Content Container
            $conditionUser = 'cuser.id IS NOT NULL AND (';                                         // user content
            $conditionUser .= '   (content.visibility = 1) OR';                                     // public visible content
            $conditionUser .= '   (content.visibility = 0' . $conditionUserPrivateRestriction . ')';  // private content of user
            if (Yii::$app->getModule('friendship')->isFriendshipEnabled()) {
                $this->leftJoin('user_friendship cff', 'cuser.id=cff.user_id AND cff.friend_user_id=:fuid', [':fuid' => $user->id]);
                $conditionUser .= ' OR (content.visibility = 0 AND cff.id IS NOT NULL)';  // users are friends
            }
            $conditionUser .= ')';

            // Created content of is always visible
            $conditionUser .= 'OR content.created_by=' . $user->id;
            $globalCondition = 'content.contentcontainer_id IS NULL';
        } elseif (AuthHelper::isGuestAccessEnabled()) {
            $conditionSpace = 'space.id IS NOT NULL and space.visibility=' . Space::VISIBILITY_ALL . ' AND content.visibility=1';
            $conditionUser = 'cuser.id IS NOT NULL and cuser.visibility=' . User::VISIBILITY_ALL . ' AND content.visibility=1';
            $globalCondition = 'content.contentcontainer_id IS NULL AND content.visibility=1';
        } else {
            return $this->emulateExecution();
        }

        $this->andWhere("{$conditionSpace} OR {$conditionUser} OR {$globalCondition}");

        return $this;
    }

    /**
     * Limits the returned records to the given ContentContainer.
     *
     * @param ContentContainerActiveRecord $container |null or null for global content
     * @return ActiveQueryContent
     * @throws Exception
     */
    public function contentContainer($container)
    {
        if ($container === null) {
            $this->joinWith(['content', 'content.contentContainer', 'content.createdBy']);
            $this->andWhere(['IS', 'contentcontainer.pk', new Expression('NULL')]);
        } else {
            $this->joinWith(['content', 'content.contentContainer', 'content.createdBy']);
            $this->andWhere(['contentcontainer.pk' => $container->id, 'contentcontainer.class' => get_class($container)]);
        }

        return $this;
    }

    /**
     * Returns only content which has one or all of given ContentTags
     *
     * @param $contentTags ContentTag[]|ContentTag
     * @param $mode string
     * @return ActiveQueryContent
     */
    public function contentTag($contentTags, $mode = 'AND')
    {
        if (!is_array($contentTags)) {
            $contentTags = [$contentTags];
        }

        if ($mode == 'AND') {
            foreach ($contentTags as $contentTag) {
                $contentTagQuery = ContentTagRelation::find()->select('content_id');
                $contentTagQuery->andWhere(['content_tag_relation.tag_id' => $contentTag->id]);
                $contentTagQuery->andWhere('content_tag_relation.content_id=content.id');
                $this->andWhere(['content.id' => $contentTagQuery]);
            }
        } elseif ($mode == 'OR') {
            $names = array_map(function ($v) {
                return $v->name;
            }, $contentTags);

            $this->joinWith('content.tags');
            $this->andWhere(['IS NOT', 'content_tag.id', new Expression('NULL')]);
            $this->andWhere(['IN', 'content_tag.name', $names]);
            $this->distinct();
        }

        return $this;
    }


    /**
     * Adds an additional WHERE condition to the existing one.
     *
     * @inheritdoc
     *
     * @param array|string $condition
     * @param array $params
     * @return $this
     */
    public function where($condition, $params = [])
    {
        return parent::andWhere($condition, $params);
    }

    /**
     * Finds user related content.
     * All available scopes: ActiveQueryContent::USER_RELATED_SCOPE_*
     *
     * @param array $scopes
     * @param User $user
     * @return ActiveQueryContent
     * @throws Throwable
     */
    public function userRelated($scopes = [], $user = null)
    {
        if ($user === null) {
            if (Yii::$app->user->isGuest) {
                return $this->andWhere('false');
            }

            $user = Yii::$app->user->getIdentity();
        }

        $this->joinWith(['content', 'content.contentContainer']);

        $conditions = [];
        $params = [];

        if (in_array(self::USER_RELATED_SCOPE_OWN_PROFILE, $scopes)) {
            $conditions[] = 'contentcontainer.pk=:userId AND class=:userClass';
            $params[':userId'] = $user->id;
            $params[':userClass'] = User::class;
        }

        if (in_array(self::USER_RELATED_SCOPE_SPACES, $scopes)) {
            $spaceMemberships = (new Query())
                ->select("sm.id")
                ->from('space_membership')
                ->leftJoin('space sm', 'sm.id=space_membership.space_id')
                ->where('space_membership.user_id=:userId AND space_membership.status=' . Membership::STATUS_MEMBER);
            $conditions[] = 'contentcontainer.pk IN (' . Yii::$app->db->getQueryBuilder()->build($spaceMemberships)[0] . ') AND contentcontainer.class = :spaceClass';
            $params[':userId'] = $user->id;
            $params[':spaceClass'] = Space::class;
        }

        if (in_array(self::USER_RELATED_SCOPE_OWN, $scopes)) {
            $conditions[] = 'content.created_by = :userId';
            $params[':userId'] = $user->id;
        }

        if (in_array(self::USER_RELATED_SCOPE_FOLLOWED_SPACES, $scopes)) {
            $spaceFollow = (new Query())
                ->select("sf.id")
                ->from('user_follow')
                ->leftJoin('space sf', 'sf.id=user_follow.object_id AND user_follow.object_model=:spaceClass')
                ->where('user_follow.user_id=:userId AND sf.id IS NOT NULL');
            $conditions[] = 'contentcontainer.pk IN (' . Yii::$app->db->getQueryBuilder()->build($spaceFollow)[0] . ') AND contentcontainer.class = :spaceClass';
            $params[':spaceClass'] = Space::class;
            $params[':userId'] = $user->id;
        }

        if (in_array(self::USER_RELATED_SCOPE_FOLLOWED_USERS, $scopes)) {
            $userFollow = (new Query())
                ->select(["uf.id"])
                ->from('user_follow')
                ->leftJoin('user uf', 'uf.id=user_follow.object_id AND user_follow.object_model=:userClass')
                ->where('user_follow.user_id=:userId AND uf.id IS NOT NULL');
            $conditions[] = 'contentcontainer.pk IN (' . Yii::$app->db->getQueryBuilder()->build($userFollow)[0] . ' AND contentcontainer.class=:userClass)';
            $params[':userClass'] = User::class;
            $params[':userId'] = $user->id;
        }

        if (count($conditions) != 0) {
            $this->andWhere("(" . join(') OR (', $conditions) . ")", $params);
        } else {
            // No results, when no selector given
            $this->andWhere('1=2');
        }

        return $this;
    }

}
