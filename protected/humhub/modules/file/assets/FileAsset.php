<?php



namespace humhub\modules\file\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\ui\view\components\View;

/**
 * Fle related assets.
 *
 * @since 1.2
 * @author buddha
 */
class FileAsset extends AssetBundle
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
    public $sourcePath = '@file/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.file.js',
    ];
}
