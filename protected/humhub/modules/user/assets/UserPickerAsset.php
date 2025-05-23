<?php



namespace humhub\modules\user\assets;

use humhub\assets\Select2Asset;
use humhub\assets\Select2SearchInputPlaceholderAsset;
use humhub\components\assets\AssetBundle;

class UserPickerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@user/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.user.picker.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        Select2Asset::class,
        Select2SearchInputPlaceholderAsset::class,
    ];
}
