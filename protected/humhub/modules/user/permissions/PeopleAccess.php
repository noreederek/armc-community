<?php



namespace humhub\modules\user\permissions;

use humhub\libs\BasePermission;
use Yii;

class PeopleAccess extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'user';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('UserModule.permissions', 'Can Access \'People\'');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('UserModule.permissions', 'Can access \'People\' section.');
    }
}
