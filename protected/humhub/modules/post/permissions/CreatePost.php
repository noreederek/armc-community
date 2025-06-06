<?php



namespace humhub\modules\post\permissions;

use humhub\libs\BasePermission;
use Yii;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;

/**
 * CreatePost Permission
 */
class CreatePost extends BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
        User::USERGROUP_SELF,
        User::USERGROUP_FRIEND,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
        User::USERGROUP_SELF,
        User::USERGROUP_GUEST,
    ];

    /**
     * @inheritdoc
     */
    protected $moduleId = 'post';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('PostModule.base', 'Create post');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        if ($this->contentContainer instanceof User) {
            return Yii::t('PostModule.base', 'Allow others to create new posts on your profile page');
        }
        return Yii::t('PostModule.base', 'Allows the user to create posts');
    }

}
