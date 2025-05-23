<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;

/**
 * jquery-At.js
 *
 * @author buddha
 */
class JplayerModuleAsset extends WebStaticAssetBundle
{
    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub/humhub.media.Jplayer.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        JplayerAsset::class,
    ];
}
