<?php



namespace humhub\modules\admin\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AdminGroupAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $jsOptions = [
        'position' => View::POS_END,
    ];
    public $sourcePath = '@admin/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.admin.group.js',
    ];

}
