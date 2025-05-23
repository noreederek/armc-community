<?php



namespace humhub\modules\admin\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * ManageModules Permission allows access to module section within the admin area.
 *
 * @since 1.2
 */
class ManageModules extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'admin_manage_modules';

    /**
     * ManageModules constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('AdminModule.permissions', 'Manage Modules');
        $this->description = Yii::t('AdminModule.permissions', 'Can manage modules within the \'Administration ->  Modules\' section.');
    }

}
