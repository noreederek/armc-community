<?php



namespace humhub\modules\user\permissions;

use humhub\libs\BasePermission;
use Yii;
use humhub\modules\user\models\User;

/**
 * ViewAboutPage Permission
 */
class ViewAboutPage extends BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        User::USERGROUP_SELF,
        User::USERGROUP_FRIEND,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_SELF,
    ];

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->title = Yii::t('UserModule.base', 'View your about page');
        $this->description = Yii::t('UserModule.base', 'Allows access to your about page with personal information');
    }

    /**
     * @inheritdoc
     */
    public function getDefaultState($groupId)
    {
        // When friendship is disabled, also allow normal members to see about page
        if ($groupId == User::USERGROUP_USER && !Yii::$app->getModule('friendship')->isFriendshipEnabled()) {
            return self::STATE_ALLOW;
        }

        return parent::getDefaultState($groupId);
    }

    /**
     * @inheritdoc
     */
    protected $title;

    /**
     * @inheritdoc
     */
    protected $description;

    /**
     * @inheritdoc
     */
    protected $moduleId = 'user';

}
