<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\ui\view\components\View;

/**
 * bluebird promis library
 *
 * @author luke
 */
class BluebirdAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $defaultDepends = false;

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
    public $sourcePath = '@npm/bluebird';

    /**
     * @inheritdoc
     */
    public $js = ['js/browser/bluebird.min.js'];
}
