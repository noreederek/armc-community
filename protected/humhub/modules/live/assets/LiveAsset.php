<?php



namespace humhub\modules\live\assets;

use humhub\components\assets\AssetBundle;

class LiveAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@live/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.live.js',
        'js/humhub.live.poll.js',
    ];
}
