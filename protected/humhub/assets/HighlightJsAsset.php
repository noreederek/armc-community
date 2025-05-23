<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;

class HighlightJsAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $js = ['js/highlight.js/highlight.pack.js'];

    /**
     * @inheritdoc
     */
    public $depends = [
        HighlightJsStyleAsset::class,
    ];
}
