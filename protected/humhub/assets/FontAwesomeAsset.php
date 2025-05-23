<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * Fontawesome
 *
 * @author luke
 */
class FontAwesomeAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/font-awesome';

    /**
     * @inheritdoc
     */
    public $css = ['css/font-awesome.min.css'];

}
