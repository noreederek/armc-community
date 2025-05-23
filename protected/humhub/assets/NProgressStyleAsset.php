<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * NProgress assets
 *
 * @since 1.2
 * @author luke
 */
class NProgressStyleAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/nprogress';

    /**
     * @inheritdoc
     */
    public $css = [
        'nprogress.css',
    ];

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'only' => [
            '/nprogress.css',
            '/nprogress.js',
        ],
    ];
}
