<?php



namespace humhub\assets;

use yii\web\AssetBundle;

/**
 * jquery-caretjs.js
 *
 * @author buddha
 */
class CaretjsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/caret.js';

    /**
     * @inheritdoc
     */
    public $js = ['dist/jquery.caret.min.js'];
}
