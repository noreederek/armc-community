<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * masonry asset class
 *
 * @author buddha
 */
class ImagesLoadedAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/imagesloaded';

    /**
     * @inheritdoc
     */
    public $js = ['imagesloaded.pkgd.min.js'];
}
