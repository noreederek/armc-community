<?php



namespace humhub\assets;

use yii\web\AssetBundle;

/**
 * select2
 *
 * @author buddha
 */
class Select2StyleAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/select2/dist/css';

    /**
     * @inheritdoc
     */
    public $css = ['select2.min.css'];
}
