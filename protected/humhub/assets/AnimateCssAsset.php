<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * animate.css
 *
 * @author buddha
 */
class AnimateCssAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $defaultDepends = false;

    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/animate.css';

    /**
     * @inheritdoc
     */
    public $css = ['animate.min.css'];

}
