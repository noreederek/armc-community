<?php



namespace humhub\modules\space\permissions;

use humhub\libs\BasePermission;
use Yii;

class SpaceDirectoryAccess extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'space';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('SpaceModule.permissions', 'Can Access \'Spaces\'');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('SpaceModule.permissions', 'Can access the \'Spaces\' section.');
    }
}
