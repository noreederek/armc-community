<?php



namespace humhub\assets;

use humhub\components\assets\AssetBundle;
use yii\web\View;

/**
 * TimeAgo Asset Bundle
 *
 * @author luke
 */
class JqueryTimeAgoAsset extends AssetBundle
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
    public $sourcePath = '@npm/timeago';

    /**
     * @inheritdoc
     */
    public $js = ['jquery.timeago.js'];

}
