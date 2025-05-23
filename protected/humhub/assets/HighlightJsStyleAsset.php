<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;

class HighlightJsStyleAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $css = ['js/highlight.js/styles/github.css'];
}
