<?php



namespace humhub\modules\space\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\user\assets\UserAsset;

class SpaceChooserAsset extends AssetBundle
{
    public $sourcePath = '@space/resources';

    public $js = [
        'js/humhub.space.chooser.js',
    ];

    public $depends = [
        SpaceAsset::class,
        UserAsset::class,
    ];
}
