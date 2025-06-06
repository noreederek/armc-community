<?php



namespace humhub\modules\topic\assets;

use humhub\components\assets\AssetBundle;

class TopicAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@topic/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.topic.js',
    ];
}
