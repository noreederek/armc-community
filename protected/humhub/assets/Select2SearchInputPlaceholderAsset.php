<?php


namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;

/**
 * Search Input Placeholder plugin for Select2
 */
class Select2SearchInputPlaceholderAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $js = ['js/select2-searchInputPlaceholder.min.js'];
}
