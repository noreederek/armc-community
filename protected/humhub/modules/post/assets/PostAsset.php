<?php



namespace humhub\modules\post\assets;

use humhub\components\assets\AssetBundle;

class PostAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@post/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.post.js',
    ];
}
