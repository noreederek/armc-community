<?php



namespace humhub\assets;

use yii\web\AssetBundle;

/**
 * jquery-highlight
 *
 * @author buddha
 */
class JqueryHighlightAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/jquery-highlight';

    /**
     * @inheritdoc
     */
    public $js = ['jquery.highlight.js'];
}
