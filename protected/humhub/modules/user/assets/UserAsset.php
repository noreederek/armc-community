<?php



namespace humhub\modules\user\assets;

use humhub\components\assets\AssetBundle;

class UserAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@user/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.user.js',
        'js/humhub.user.login.js',
    ];

}
