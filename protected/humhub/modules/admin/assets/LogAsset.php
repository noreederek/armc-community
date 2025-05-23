<?php



namespace humhub\modules\admin\assets;

use yii\web\AssetBundle;
use yii\web\View;

class LogAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@admin/resources';

    /**
     * @inheritdoc
     */
    public $jsOptions = [
        'position' => View::POS_END,
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.admin.log.js',
    ];

}
