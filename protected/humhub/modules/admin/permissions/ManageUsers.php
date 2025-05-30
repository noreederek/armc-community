<?php



namespace humhub\modules\admin\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * ManageUsers Permission allows access to users/userstab section within the admin area.
 *
 * @since 1.2
 */
class ManageUsers extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'admin_manage_users';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('AdminModule.permissions', 'Manage Users');
        $this->description = Yii::t('AdminModule.permissions', 'Can manage users and user profiles.');
    }

}
