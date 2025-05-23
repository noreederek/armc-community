<?php



namespace humhub\modules\admin\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * ManageSpaces permission allows access to users/spaces section within the admin area.
 *
 * @since 1.2
 */
class ManageSpaces extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'admin_manage_spaces';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('AdminModule.permissions', 'Manage Spaces');
        $this->description = Yii::t('AdminModule.permissions', 'Can manage Spaces within the \'Administration -> Spaces\' section.');
    }

}
