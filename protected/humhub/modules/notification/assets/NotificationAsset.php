<?php



namespace humhub\modules\notification\assets;

use humhub\components\assets\AssetBundle;

class NotificationAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@notification/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.notification.js',
    ];
}
