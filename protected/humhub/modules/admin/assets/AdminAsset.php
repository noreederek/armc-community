<?php



namespace humhub\modules\admin\assets;

use humhub\components\assets\AssetBundle;
use yii\web\View;

class AdminAsset extends AssetBundle
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
        'js/humhub.admin.js',
    ];
}
