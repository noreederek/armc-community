<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * jquery-autosize
 *
 * @author buddha
 */
class JqueryAutosizeAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/jquery-autosize';

    /**
     * @inheritdoc
     */
    public $js = ['jquery.autosize.min.js'];
}
