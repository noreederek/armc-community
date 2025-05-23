<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * NProgress assets
 *
 * @since 1.2
 * @author luke
 */
class NProgressAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/nprogress';

    /**
     * @inheritdoc
     */
    public $js = [
        'nprogress.js',
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

    /**
     * @inheritdoc
     */
    public $depends = [
        NProgressStyleAsset::class,
    ];
}
