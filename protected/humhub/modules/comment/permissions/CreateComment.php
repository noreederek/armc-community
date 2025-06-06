<?php



namespace humhub\modules\comment\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

/**
 * CreateComment Permission
 */
class CreateComment extends BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
        Space::USERGROUP_USER,
        User::USERGROUP_USER,
        User::USERGROUP_SELF,
        User::USERGROUP_FRIEND,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
        User::USERGROUP_GUEST,
    ];

    /**
     * @inheritdoc
     */
    protected $title = 'Create comment';

    /**
     * @inheritdoc
     */
    protected $description = 'Allows the user to add comments';

    /**
     * @inheritdoc
     */
    protected $moduleId = 'comment';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('CommentModule.permissions', 'Create comment');
        $this->description = Yii::t('CommentModule.permissions', 'Allows the user to add comments');
    }

}
