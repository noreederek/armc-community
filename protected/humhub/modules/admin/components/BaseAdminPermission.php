<?php



namespace humhub\modules\admin\components;

use humhub\libs\BasePermission;
use humhub\modules\user\models\Group;

/**
 * BaseAdminPermission is a fixed allowed permission for the admin group
 *
 * @author buddha
 * @since 1.2
 */
class BaseAdminPermission extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $moduleId = 'admin';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_DENY;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->fixedGroups[] = Group::getAdminGroupId();

        parent::init();
    }

    /**
     * {@inheritdoc}
     *
     * Note: that this function always returns state self::STATE_ALLOW for the administration
     * group, this behaviour can't be overwritten by means of the configuration.
     *
     * Thi
     * @param type $groupId
     * @return type
     */
    public function getDefaultState($groupId)
    {
        if ($groupId == Group::getAdminGroupId()) {
            return self::STATE_ALLOW;
        }

        return parent::getDefaultState($groupId);
    }

}
