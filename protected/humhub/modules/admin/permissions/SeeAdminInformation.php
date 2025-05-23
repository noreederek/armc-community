<?php



namespace humhub\modules\admin\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * SeeAdminInformation Permission allows access to information section within the admin area.
 *
 * @since 1.2
 */
class SeeAdminInformation extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'admin_see_information';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('AdminModule.permissions', 'Access Admin Information');
        $this->description = Yii::t('AdminModule.permissions', 'Can access the \'Administration -> Information\' section.');
    }

}
