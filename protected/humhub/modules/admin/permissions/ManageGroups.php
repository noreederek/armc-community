<?php



namespace humhub\modules\admin\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * ManageUsersAdvanced Permission allows access to users/userstab section within the admin area.
 *
 * @since 1.2
 */
class ManageGroups extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'admin_manage_groups';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('AdminModule.permissions', 'Manage Groups');
        $this->description = Yii::t('AdminModule.permissions', 'Can manage users and groups');
    }

}
