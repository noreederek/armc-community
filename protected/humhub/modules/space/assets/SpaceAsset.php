<?php



namespace humhub\modules\space\assets;

use humhub\components\assets\AssetBundle;

class SpaceAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@space/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.space.js',
    ];
}
