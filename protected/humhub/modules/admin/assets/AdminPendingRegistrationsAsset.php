<?php



namespace humhub\modules\admin\assets;

use humhub\components\assets\AssetBundle;
use yii\web\View;

class AdminPendingRegistrationsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $jsOptions = [
        'position' => View::POS_END,
    ];

    /**
     * @inheritdoc
     */
    public $sourcePath = '@admin/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.admin.PendingRegistrations.js',
    ];

}
