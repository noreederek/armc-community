<?php



namespace humhub\modules\space\permissions;

use humhub\modules\admin\components\BaseAdminPermission;
use Yii;

/**
 * CreatePrivateSpace Permission
 */
class CreatePrivateSpace extends BaseAdminPermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'create_private_space';

    /**
     * @inheritdoc
     */
    protected $title = 'Create Private Spaces';

    /**
     * @inheritdoc
     */
    protected $description = 'Can create hidden (private) Spaces.';

    /**
     * @inheritdoc
     */
    protected $moduleId = 'space';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('SpaceModule.permissions', 'Create Private Spaces');
        $this->description = Yii::t('SpaceModule.permissions', 'Can create hidden (private) Spaces.');
    }
}
