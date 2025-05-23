<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;

/**
 * jquery-ui-widget
 *
 * @author luke
 */
class JqueryWidgetAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/jquery-ui';

    /**
     * @inheritdoc
     */
    public $js = ['ui/minified/widget.js'];

}
