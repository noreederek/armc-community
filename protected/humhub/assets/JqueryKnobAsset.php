<?php



namespace humhub\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * jquery-knob
 *
 * @author luke
 */
class JqueryKnobAsset extends AssetBundle
{
    public $jsOptions = ['position' => View::POS_BEGIN];

    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/jquery-knob';

    /**
     * @inheritdoc
     */
    public $js = ['dist/jquery.knob.min.js'];

    public $depends = ['humhub\assets\AppAsset'];

}
