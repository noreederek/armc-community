<?php



namespace humhub\modules\user\assets;

use humhub\components\assets\AssetBundle;

class PermissionGridModuleFilterAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@user/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.user.PermissionGridModuleFilter.js',
    ];
}
