<?php



namespace humhub\assets;

use yii\web\AssetBundle;

/**
 * jquery-cookie
 *
 * @author buddha
 */
class JqueryCookieAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/jquery.cookie';

    /**
     * @inheritdoc
     */
    public $js = ['jquery.cookie.js'];

}
