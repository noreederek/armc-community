<?php



namespace humhub\modules\admin\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * ManageSettings Permission allows access to settings section within the admin area.
 *
 * @since 1.2
 */
class ManageSettings extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'admin_manage_settings';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('AdminModule.permissions', 'Manage Settings');
        $this->description = Yii::t('AdminModule.permissions', 'Can manage general settings.');
    }

}
