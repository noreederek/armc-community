<?php



namespace humhub\modules\comment\assets;

use humhub\components\assets\AssetBundle;

class CommentAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@comment/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.comment.js',
    ];
}
