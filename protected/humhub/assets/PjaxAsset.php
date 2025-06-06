<?php



namespace humhub\assets;

use humhub\components\assets\WebStaticAssetBundle;
use yii\web\View;

/**
 * select2
 *
 * @author buddha
 */
class PjaxAsset extends WebStaticAssetBundle
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
    public $js = ['js/jquery.pjax.modified.js'];
}
