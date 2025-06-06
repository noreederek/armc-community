<?php



namespace humhub\modules\content\assets;

use humhub\components\assets\AssetBundle;

/**
 * Content container asset for shared user/space js functionality.
 *
 * @since 1.2
 * @author buddha
 */
class ContentContainerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@content/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.content.container.js',
    ];

}
