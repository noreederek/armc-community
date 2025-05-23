<?php



namespace humhub\modules\content\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\ui\view\components\View;

/**
 * Asset for core content resources.
 *
 * @since 1.2
 * @author buddha
 */
class ContentAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $defer = false;

    /**
     * @inheritdoc
     */
    public $jsPosition = View::POS_HEAD;

    /**
     * @inheritdoc
     */
    public $sourcePath = '@content/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.content.js',
    ];
}
